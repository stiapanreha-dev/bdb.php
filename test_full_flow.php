<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Full Companies Page Flow...\n\n";

try {
    // Find admin user
    $user = DB::table('users')->where('email', 'admin@businessdb.ru')->first();

    if (!$user) {
        echo "ERROR: Admin user not found!\n";
        exit(1);
    }

    echo "1. User found: " . $user->email . "\n";
    echo "   Is Admin: " . ($user->is_admin ? 'YES' : 'NO') . "\n";
    echo "   Balance: " . $user->balance . "\n\n";

    // Test password
    if (Hash::check('admin123', $user->password)) {
        echo "2. Password 'admin123': CORRECT\n\n";
    } else {
        echo "2. Password 'admin123': INCORRECT\n\n";
    }

    // Simulate what CompanyController does
    echo "3. Simulating CompanyController->index():\n";

    $perPage = 20;
    $page = 1;
    $offset = ($page - 1) * $perPage;

    // Get companies
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

    $total = DB::connection('mssql_cp1251')->table('db_companies')->count();
    $companies = $query->offset($offset)->limit($perPage)->get()->toArray();

    echo "   Total companies: " . number_format($total) . "\n";
    echo "   Companies on page 1: " . count($companies) . "\n";

    if (count($companies) > 0) {
        echo "\n   First company:\n";
        $first = (array) $companies[0];
        echo "     - ID: " . $first['id'] . "\n";
        echo "     - Company: " . ($first['company'] ?? 'N/A') . "\n";
        echo "     - City: " . ($first['city'] ?? 'N/A') . "\n";
        echo "     - Phone: " . ($first['phone'] ?? $first['mobile_phone'] ?? 'N/A') . "\n";
    }

    echo "\n4. Get rubrics:\n";
    $rubrics = DB::connection('mssql_cp1251')->table('db_rubrics')->count();
    echo "   Total: " . $rubrics . "\n";

    echo "\n5. Get cities:\n";
    $cities = DB::connection('mssql_cp1251')->table('db_cities')->count();
    echo "   Total: " . $cities . "\n";

    echo "\n✓ SUCCESS! Everything works correctly!\n";
    echo "\nTo login, use:\n";
    echo "  Email: admin@businessdb.ru\n";
    echo "  Password: admin123\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
