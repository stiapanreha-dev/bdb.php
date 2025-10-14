<?php
// Тест прямого подключения к MSSQL
$serverName = "172.26.192.1,1433";
$connectionOptions = array(
    "Database" => "buss",
    "Uid" => "sa",
    "PWD" => "123123123",
    "TrustServerCertificate" => true,
    "Encrypt" => false
);

echo "Attempting to connect to SQL Server...\n";
echo "Server: $serverName\n";
echo "Database: buss\n\n";

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    echo "Connection failed!\n";
    die(print_r(sqlsrv_errors(), true));
}

echo "✓ Connected successfully!\n\n";

// Тест запроса
echo "Testing query: SELECT TOP 1 id, purchase_object FROM zakupki\n\n";

$sql = "SELECT TOP 1 id, purchase_object FROM zakupki";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    echo "Query failed!\n";
    die(print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "✓ Query successful!\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Purchase Object: " . substr($row['purchase_object'], 0, 100) . "...\n";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo "\n✓ All tests passed!\n";
?>
