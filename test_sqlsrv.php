<?php
echo "Testing SQL Server connection...\n\n";

// Check if extensions are loaded
echo "pdo_sqlsrv extension loaded: " . (extension_loaded('pdo_sqlsrv') ? 'YES' : 'NO') . "\n";
echo "sqlsrv extension loaded: " . (extension_loaded('sqlsrv') ? 'YES' : 'NO') . "\n\n";

// Try PDO connection
try {
    $dsn = "sqlsrv:Server=localhost;Database=business";
    $conn = new PDO($dsn, 'sa', 'gtnhjd');
    echo "PDO Connection: SUCCESS\n";

    $stmt = $conn->query("SELECT TOP 1 company FROM db_companies");
    $result = $stmt->fetchColumn();
    echo "Query result: " . $result . "\n";

} catch (PDOException $e) {
    echo "PDO Connection FAILED: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
