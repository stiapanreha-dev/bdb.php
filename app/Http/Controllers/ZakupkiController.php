<?php

namespace App\Http\Controllers;

use App\Helpers\DataMaskingHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ZakupkiController extends Controller
{
    const MAX_SEARCH_DAYS = 30;
    const VALID_PER_PAGE = [20, 50, 100, 500];

    /**
     * Display the zakupki index page.
     */
    public function index(Request $request)
    {
        // Validate and get per_page parameter
        $perPage = $this->getPerPage($request);
        $page = max(1, (int) $request->get('page', 1));

        // Get filter parameters
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $searchText = trim($request->get('search_text', ''));

        // Convert dates
        $dateFromObj = $dateFrom ? Carbon::parse($dateFrom) : null;
        $dateToObj = $dateTo ? Carbon::parse($dateTo) : null;

        // Apply date range validation for search
        $this->validateSearchDateRange($searchText, $dateFromObj, $dateToObj);

        // Determine access level and masking
        $restrictToIds = null;
        $hasFullAccess = false;
        $showMaskedData = true;

        if (!Auth::check()) {
            // Unauthenticated: top 50 only, masked data
            $maxRecords = 50;
            $offset = ($page - 1) * $perPage;

            if ($offset >= $maxRecords) {
                $offset = 0;
                $page = 1;
            }

            $limit = min($perPage, $maxRecords - $offset);
            $restrictToIds = $this->getTop50Ids();
            $hasFullAccess = false;
            $showMaskedData = true;
        } elseif (Auth::user()->hasActiveSubscription() || Auth::user()->isAdmin()) {
            // Subscribed users or admin: full access, unmasked data
            $limit = $perPage;
            $offset = ($page - 1) * $perPage;
            $hasFullAccess = true;
            $showMaskedData = false;
        } else {
            // Registered but unpaid: full access, masked data
            $limit = $perPage;
            $offset = ($page - 1) * $perPage;
            $hasFullAccess = true;
            $showMaskedData = true;
        }

        // Get data from MSSQL
        $result = $this->getZakupki(
            $dateFromObj,
            $dateToObj,
            $searchText ?: null,
            $limit,
            $offset,
            $restrictToIds,
            !Auth::check() // count_all for unauthenticated
        );

        // Apply data masking
        if ($showMaskedData) {
            $result['data'] = DataMaskingHelper::applyMasking(
                $result['data'],
                !$showMaskedData,
                ['email' => 'email', 'phone' => 'phone', 'site' => 'site']
            );
        }

        return view('zakupki.index', [
            'zakupki' => $result['data'],
            'total' => $result['total'],
            'date_from' => $dateFrom ?? '',
            'date_to' => $dateTo ?? '',
            'search_text' => $searchText,
            'has_full_access' => $hasFullAccess,
            'show_masked_email' => $showMaskedData,
            'show_masked_phone' => $showMaskedData,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Show zakupki detail page.
     */
    public function show(Request $request, int $id)
    {
        // Get filter parameters for breadcrumb navigation
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $searchText = $request->get('search_text', '');
        $page = $request->get('page', '1');
        $perPage = $request->get('per_page', '20');

        // Get zakupka data
        $result = $this->getZakupki(null, null, null, 1, 0, [$id]);

        if (empty($result['data'])) {
            return redirect()->route('zakupki.index')
                ->with('error', 'Закупка не найдена');
        }

        $zakupka = $result['data'][0];

        // Get specifications
        $specifications = $this->getSpecifications($id);

        // Determine masking
        $showMaskedData = !(Auth::check() && (Auth::user()->hasActiveSubscription() || Auth::user()->isAdmin()));

        // Apply masking
        if ($showMaskedData) {
            $zakupka = DataMaskingHelper::applyMasking(
                [$zakupka],
                false,
                ['email' => 'email', 'phone' => 'phone', 'site' => 'site']
            )[0];
        }

        return view('zakupki.detail', [
            'zakupka' => $zakupka,
            'specifications' => $specifications,
            'show_masked_email' => $showMaskedData,
            'show_masked_phone' => $showMaskedData,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'search_text' => $searchText,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Export zakupki to Excel.
     */
    public function export(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $searchText = trim($request->get('search_text', ''));

        // Convert dates
        $dateFromObj = $dateFrom ? Carbon::parse($dateFrom) : null;
        $dateToObj = $dateTo ? Carbon::parse($dateTo) : null;

        // Apply date range validation
        $this->validateSearchDateRange($searchText, $dateFromObj, $dateToObj);

        // Get data (limit 10000)
        $result = $this->getZakupki(
            $dateFromObj,
            $dateToObj,
            $searchText ?: null,
            10000,
            0
        );

        // Create Excel file using Maatwebsite Excel
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ZakupkiExport($result['data']),
            'zakupki_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Get zakupki data from MSSQL database.
     */
    private function getZakupki(
        ?Carbon $dateFrom,
        ?Carbon $dateTo,
        ?string $searchText,
        int $limit,
        int $offset,
        ?array $restrictToIds = null,
        bool $countAll = false
    ): array {
        $query = DB::connection('mssql')->table('zakupki as z')
            ->select([
                'z.id',
                'z.created as date_request',
                'z.purchase_object',
                'z.start_cost_var',
                'z.start_cost',
                'z.customer',
                DB::raw('ISNULL(z.email, z.additional_contacts) as email'),
                'z.contact_number as phone',
                'z.post_address as address',
                'z.purchase_type',
            ])
            ->orderBy('z.id', 'desc');

        $countQuery = DB::connection('mssql')->table('zakupki as z');

        // Apply restrict_to_ids filter (for unauthenticated users)
        if ($restrictToIds !== null) {
            if (empty($restrictToIds)) {
                return ['data' => [], 'total' => 0];
            }
            $query->whereIn('z.id', $restrictToIds);

            // Don't apply restriction to count query if countAll is true
            if (!$countAll) {
                $countQuery->whereIn('z.id', $restrictToIds);
            }
        }

        // Apply date filters (use CAST for SQL Server datetime comparison)
        if ($dateFrom) {
            $dateFromStr = $dateFrom->format('Y-m-d H:i:s');
            $query->whereRaw("z.created >= CAST(? AS DATETIME)", [$dateFromStr]);
            $countQuery->whereRaw("z.created >= CAST(? AS DATETIME)", [$dateFromStr]);
        }

        if ($dateTo) {
            $dateToStr = $dateTo->format('Y-m-d H:i:s');
            $query->whereRaw("z.created <= CAST(? AS DATETIME)", [$dateToStr]);
            $countQuery->whereRaw("z.created <= CAST(? AS DATETIME)", [$dateToStr]);
        }

        // Apply search filter
        if ($searchText) {
            $query->where(function ($q) use ($searchText) {
                $q->where('z.purchase_object', 'like', "%{$searchText}%")
                  ->orWhere('z.customer', 'like', "%{$searchText}%");
            });
            $countQuery->where(function ($q) use ($searchText) {
                $q->where('z.purchase_object', 'like', "%{$searchText}%")
                  ->orWhere('z.customer', 'like', "%{$searchText}%");
            });
        }

        // Get total count
        $total = $countQuery->count();

        // Get paginated data
        $data = $query->offset($offset)->limit($limit)->get()->toArray();

        // Convert stdClass to array
        $data = array_map(fn($item) => (array) $item, $data);

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * Get specifications for a zakupka.
     */
    private function getSpecifications(int $zakupkiId): array
    {
        $data = DB::connection('mssql')->table('zakupki_specification')
            ->select([
                'id',
                'id_zakupki',
                'product',
                'product_specification',
                'quantity',
                'price_vat',
                'terms_of_payment',
                'delivery_time',
            ])
            ->where('id_zakupki', $zakupkiId)
            ->orderBy('id')
            ->get()
            ->toArray();

        return array_map(fn($item) => (array) $item, $data);
    }

    /**
     * Get top 50 zakupki IDs for unauthenticated users.
     */
    private function getTop50Ids(): array
    {
        $results = DB::connection('mssql')->table('zakupki')
            ->select('id')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return $results->pluck('id')->toArray();
    }

    /**
     * Validate per_page parameter.
     */
    private function getPerPage(Request $request): int
    {
        $perPage = (int) $request->get('per_page', 20);

        if (!in_array($perPage, self::VALID_PER_PAGE)) {
            $perPage = 20;
        }

        return $perPage;
    }

    /**
     * Validate and adjust search date range.
     */
    private function validateSearchDateRange(string $searchText, ?Carbon &$dateFrom, ?Carbon &$dateTo): void
    {
        if (!$searchText) {
            return;
        }

        // If search text but no dates: set last 30 days
        if (!$dateFrom && !$dateTo) {
            $dateTo = Carbon::now();
            $dateFrom = $dateTo->copy()->subDays(self::MAX_SEARCH_DAYS);
            session()->flash('info', "Поиск выполнен за последние " . self::MAX_SEARCH_DAYS . " дней. Для изменения периода укажите даты.");
            return;
        }

        // If only one date: add the other
        if ($dateFrom && !$dateTo) {
            $dateTo = $dateFrom->copy()->addDays(self::MAX_SEARCH_DAYS);
            session()->flash('info', "Период поиска ограничен " . self::MAX_SEARCH_DAYS . " днями от указанной даты.");
        } elseif ($dateTo && !$dateFrom) {
            $dateFrom = $dateTo->copy()->subDays(self::MAX_SEARCH_DAYS);
            session()->flash('info', "Период поиска ограничен " . self::MAX_SEARCH_DAYS . " днями до указанной даты.");
        }

        // Limit interval to MAX_SEARCH_DAYS
        if ($dateFrom && $dateTo) {
            $daysDiff = $dateFrom->diffInDays($dateTo);
            if ($daysDiff > self::MAX_SEARCH_DAYS) {
                $dateFrom = $dateTo->copy()->subDays(self::MAX_SEARCH_DAYS);
                session()->flash('warning', "Интервал поиска ограничен " . self::MAX_SEARCH_DAYS . " днями. Период скорректирован.");
            }
        }
    }
}
