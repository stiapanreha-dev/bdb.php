<?php
// Simulate authenticated request to companies page
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Create kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create request
$request = Illuminate\Http\Request::create(
    'https://businessdb.ru/companies',
    'GET'
);

// Find admin user and simulate authentication
$user = App\Models\User::where('username', 'admin')->first();

if (!$user) {
    echo "ERROR: User not found\n";
    exit(1);
}

// Set authenticated user
Auth::login($user);
$request->setUserResolver(function () use ($user) {
    return $user;
});

echo "Testing Companies Page as authenticated user...\n\n";
echo "User: " . $user->username . " (balance: " . $user->balance . ")\n\n";

try {
    // Handle request
    $response = $kernel->handle($request);

    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Headers:\n";
    foreach ($response->headers->all() as $key => $values) {
        echo "  $key: " . implode(', ', $values) . "\n";
    }

    $content = $response->getContent();
    echo "\nResponse Length: " . strlen($content) . " bytes\n";

    // Check if response contains expected content
    if (strpos($content, 'Предприятия России') !== false) {
        echo "✓ Found header 'Предприятия России'\n";
    } else {
        echo "✗ Header 'Предприятия России' NOT FOUND\n";
    }

    if (strpos($content, 'Фильтр') !== false) {
        echo "✓ Found 'Фильтр' section\n";
    } else {
        echo "✗ 'Фильтр' section NOT FOUND\n";
    }

    if (strpos($content, 'table') !== false) {
        echo "✓ Found table element\n";
    } else {
        echo "✗ Table element NOT FOUND\n";
    }

    // Save first 5000 chars to file for inspection
    file_put_contents(__DIR__ . '/response_sample.html', substr($content, 0, 5000));
    echo "\nFirst 5000 chars saved to response_sample.html\n";

    // Check for errors in HTML
    if (strpos($content, 'error') !== false || strpos($content, 'Error') !== false) {
        echo "\n⚠ Response may contain errors\n";
    }

    $kernel->terminate($request, $response);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
