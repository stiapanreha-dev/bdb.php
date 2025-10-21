<?php

namespace App\Http\Controllers;

use App\Helpers\DataMaskingHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    const VALID_PER_PAGE = [20, 50, 100, 500];

    /**
     * Display the companies index page.
     */
    public function index(Request $request)
    {
        // Validate and get per_page parameter
        $perPage = $this->getPerPage($request);
        $page = max(1, (int) $request->get('page', 1));

        // Get filter parameters
        $idRubric = $request->get('id_rubric', null);
        $idSubrubric = $request->get('id_subrubric', null);
        $idCity = $request->get('id_city', null);
        $searchText = trim($request->get('search_text', ''));

        // Get filter lists
        $rubrics = $this->getRubrics();
        $subrubrics = $idRubric ? $this->getSubrubrics($idRubric) : [];
        $cities = $this->getCities();

        // Determine access level and masking
        $hasFullAccess = Auth::user()->hasPositiveBalance() || Auth::user()->isAdmin();
        $showMaskedData = !$hasFullAccess;

        // Pagination
        $limit = $perPage;
        $offset = ($page - 1) * $perPage;

        // Get data from MSSQL
        $result = $this->getCompanies(
            $idRubric,
            $idSubrubric,
            $idCity,
            $searchText ?: null,
            $limit,
            $offset
        );

        // DEBUG: Log the result
        \Log::info('CompanyController: Got ' . count($result['data']) . ' companies, total=' . $result['total'] . ', hasFullAccess=' . ($hasFullAccess ? 'YES' : 'NO'));

        // Apply data masking
        if ($showMaskedData) {
            $result['data'] = DataMaskingHelper::applyMasking(
                $result['data'],
                !$showMaskedData,
                ['email' => 'Email', 'phone' => 'phone', 'site' => 'site']
            );
        }

        return view('companies.index', [
            'companies' => $result['data'],
            'total' => $result['total'],
            'rubrics' => $rubrics,
            'subrubrics' => $subrubrics,
            'cities' => $cities,
            'id_rubric' => $idRubric,
            'id_subrubric' => $idSubrubric,
            'id_city' => $idCity,
            'search_text' => $searchText,
            'has_full_access' => $hasFullAccess,
            'show_masked_email' => $showMaskedData,
            'show_masked_phone' => $showMaskedData,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Show company detail page.
     */
    public function show(Request $request, int $id)
    {
        // Get filter parameters for breadcrumb navigation
        $idRubric = $request->get('id_rubric', null);
        $idSubrubric = $request->get('id_subrubric', null);
        $idCity = $request->get('id_city', null);
        $searchText = $request->get('search_text', '');
        $page = $request->get('page', '1');
        $perPage = $request->get('per_page', '20');

        // Get company data by ID (using cp1251 connection)
        $company = DB::connection('mssql_cp1251')->table('db_companies as c')
            ->leftJoin('db_rubrics as r', 'c.id_rubric', '=', 'r.id')
            ->leftJoin('db_subrubrics as sr', 'c.id_subrubric', '=', 'sr.id')
            ->leftJoin('db_cities as ct', 'c.id_city', '=', 'ct.id')
            ->select([
                'c.id',
                'c.company',
                'c.phone',
                'c.mobile_phone',
                'c.Email',
                'c.site',
                'c.inn',
                'c.ogrn',
                'c.director',
                'r.rubric',
                'sr.subrubric',
                'ct.city',
            ])
            ->where('c.id', $id)
            ->first();

        if (!$company) {
            return redirect()->route('companies.index')
                ->with('error', 'Компания не найдена');
        }

        // Convert to array
        $company = (array) $company;

        // Determine masking
        $showMaskedData = !(Auth::user()->hasPositiveBalance() || Auth::user()->isAdmin());

        // Apply masking
        if ($showMaskedData) {
            $company = DataMaskingHelper::applyMasking(
                [$company],
                false,
                ['email' => 'Email', 'phone' => 'phone', 'site' => 'site']
            )[0];
        }

        return view('companies.detail', [
            'company' => $company,
            'show_masked_email' => $showMaskedData,
            'show_masked_phone' => $showMaskedData,
            'id_rubric' => $idRubric,
            'id_subrubric' => $idSubrubric,
            'id_city' => $idCity,
            'search_text' => $searchText,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Export companies to Excel.
     */
    public function export(Request $request)
    {
        $idRubric = $request->get('id_rubric', null);
        $idSubrubric = $request->get('id_subrubric', null);
        $idCity = $request->get('id_city', null);
        $searchText = trim($request->get('search_text', ''));

        // Get data (limit 10000)
        $result = $this->getCompanies(
            $idRubric,
            $idSubrubric,
            $idCity,
            $searchText ?: null,
            10000,
            0
        );

        // Create Excel file using Maatwebsite Excel
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CompaniesExport($result['data']),
            'companies_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Get companies data from MSSQL database (cp1251).
     */
    private function getCompanies(
        ?int $idRubric,
        ?int $idSubrubric,
        ?int $idCity,
        ?string $searchText,
        int $limit,
        int $offset
    ): array {
        // Use cp1251 connection for VARCHAR fields
        $query = DB::connection('mssql_cp1251')->table('db_companies as c')
            ->leftJoin('db_rubrics as r', 'c.id_rubric', '=', 'r.id')
            ->leftJoin('db_subrubrics as sr', 'c.id_subrubric', '=', 'sr.id')
            ->leftJoin('db_cities as ct', 'c.id_city', '=', 'ct.id')
            ->select([
                'c.id',
                'c.company',
                'c.phone',
                'c.mobile_phone',
                'c.Email',
                'c.site',
                'c.inn',
                'c.ogrn',
                'c.director',
                'r.rubric',
                'sr.subrubric',
                'ct.city',
            ])
            ->orderBy('c.id', 'desc');

        $countQuery = DB::connection('mssql_cp1251')->table('db_companies as c');

        // Apply filters
        if ($idRubric) {
            $query->where('c.id_rubric', $idRubric);
            $countQuery->where('c.id_rubric', $idRubric);
        }

        if ($idSubrubric) {
            $query->where('c.id_subrubric', $idSubrubric);
            $countQuery->where('c.id_subrubric', $idSubrubric);
        }

        if ($idCity) {
            $query->where('c.id_city', $idCity);
            $countQuery->where('c.id_city', $idCity);
        }

        if ($searchText) {
            $searchCallback = function ($q) use ($searchText) {
                $q->where('c.company', 'like', "%{$searchText}%")
                  ->orWhere('c.inn', 'like', "%{$searchText}%")
                  ->orWhere('c.director', 'like', "%{$searchText}%");
            };
            $query->where($searchCallback);
            $countQuery->where($searchCallback);
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
     * Get rubrics list (cp1251).
     */
    private function getRubrics(): array
    {
        $data = DB::connection('mssql_cp1251')->table('db_rubrics')
            ->select('id', 'rubric')
            ->orderBy('rubric')
            ->get()
            ->toArray();

        return array_map(fn($item) => (array) $item, $data);
    }

    /**
     * Get subrubrics list (cp1251).
     */
    private function getSubrubrics(?int $idRubric = null): array
    {
        $query = DB::connection('mssql_cp1251')->table('db_subrubrics')
            ->select('id', 'id_rubric', 'subrubric')
            ->orderBy('subrubric');

        if ($idRubric) {
            $query->where('id_rubric', $idRubric);
        }

        $data = $query->get()->toArray();

        return array_map(fn($item) => (array) $item, $data);
    }

    /**
     * Get cities list (cp1251).
     */
    private function getCities(): array
    {
        $data = DB::connection('mssql_cp1251')->table('db_cities')
            ->select('id', 'city')
            ->orderBy('city')
            ->get()
            ->toArray();

        return array_map(fn($item) => (array) $item, $data);
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
}
