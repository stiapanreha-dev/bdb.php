<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Laravel MSSQL Connection...\n";
echo "=====================================\n\n";

try {
    echo "1. Testing basic connection...\n";
    $pdo = DB::connection('mssql')->getPdo();
    echo "   ✓ PDO connection established\n\n";

    echo "2. Testing query...\n";
    $result = DB::connection('mssql')->select('SELECT TOP 1 id, purchase_object FROM zakupki');
    echo "   ✓ Query successful!\n\n";

    echo "3. Results:\n";
    if (!empty($result)) {
        $row = $result[0];
        echo "   ID: " . $row->id . "\n";
        echo "   Purchase Object: " . substr($row->purchase_object, 0, 100) . "...\n";
    }

    echo "\n✓ All tests passed!\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nFull error:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
