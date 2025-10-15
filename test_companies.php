<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Companies Data...\n\n";

try {
    // Test mssql_cp1251 connection
    echo "1. Testing mssql_cp1251 connection:\n";
    $count = DB::connection('mssql_cp1251')->table('db_companies')->count();
    echo "   Total companies: " . $count . "\n\n";

    // Get first company
    echo "2. Getting first company:\n";
    $company = DB::connection('mssql_cp1251')->table('db_companies')
        ->select('id', 'company', 'phone', 'Email')
        ->first();

    if ($company) {
        echo "   ID: " . $company->id . "\n";
        echo "   Company: " . $company->company . "\n";
        echo "   Phone: " . ($company->phone ?? 'N/A') . "\n";
        echo "   Email: " . ($company->Email ?? 'N/A') . "\n\n";
    }

    // Test rubrics
    echo "3. Testing rubrics:\n";
    $rubrics = DB::connection('mssql_cp1251')->table('db_rubrics')->count();
    echo "   Total rubrics: " . $rubrics . "\n\n";

    // Test cities
    echo "4. Testing cities:\n";
    $cities = DB::connection('mssql_cp1251')->table('db_cities')->count();
    echo "   Total cities: " . $cities . "\n\n";

    echo "SUCCESS! All connections work!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
