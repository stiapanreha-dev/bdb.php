<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Companies Page with Authenticated User...\n\n";

try {
    // Find admin user
    $user = App\Models\User::where('username', 'admin')->first();

    if (!$user) {
        echo "ERROR: User 'admin' not found!\n";
        exit(1);
    }

    echo "1. User authenticated:\n";
    echo "   Username: " . $user->username . "\n";
    echo "   Email: " . $user->email . "\n";
    echo "   Role: " . $user->role . "\n";
    echo "   Balance: " . $user->balance . "\n";
    echo "   isAdmin(): " . ($user->isAdmin() ? 'true' : 'false') . "\n";
    echo "   hasPositiveBalance(): " . ($user->hasPositiveBalance() ? 'true' : 'false') . "\n\n";

    // Determine access level (like in CompanyController)
    $hasFullAccess = $user->hasPositiveBalance() || $user->isAdmin();
    $showMaskedData = !$hasFullAccess;

    echo "2. Access level:\n";
    echo "   hasFullAccess: " . ($hasFullAccess ? 'true' : 'false') . "\n";
    echo "   showMaskedData: " . ($showMaskedData ? 'true' : 'false') . "\n\n";

    // Get companies (like in CompanyController)
    echo "3. Fetching companies data:\n";

    $perPage = 20;
    $page = 1;
    $offset = ($page - 1) * $perPage;

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

    $total = $countQuery->count();
    $companies = $query->offset($offset)->limit($perPage)->get()->toArray();

    echo "   Total companies: " . number_format($total) . "\n";
    echo "   Companies fetched: " . count($companies) . "\n\n";

    if (count($companies) > 0) {
        echo "4. Sample company data:\n";
        $first = (array) $companies[0];
        echo "   ID: " . $first['id'] . "\n";
        echo "   Company: " . ($first['company'] ?? 'N/A') . "\n";
        echo "   Rubric: " . ($first['rubric'] ?? 'N/A') . "\n";
        echo "   City: " . ($first['city'] ?? 'N/A') . "\n";
        echo "   Phone: " . ($first['phone'] ?? $first['mobile_phone'] ?? 'N/A') . "\n";
        echo "   Email: " . ($first['Email'] ?? 'N/A') . "\n";
        echo "   INN: " . ($first['inn'] ?? 'N/A') . "\n\n";
    }

    // Get filter data
    echo "5. Fetching filter data:\n";
    $rubrics = DB::connection('mssql_cp1251')->table('db_rubrics')->count();
    $cities = DB::connection('mssql_cp1251')->table('db_cities')->count();

    echo "   Rubrics: " . $rubrics . "\n";
    echo "   Cities: " . $cities . "\n\n";

    echo "✓✓✓ SUCCESS! Everything works correctly! ✓✓✓\n";
    echo "\nThe page should display " . count($companies) . " companies.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
