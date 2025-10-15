<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "All users in database:\n\n";

try {
    $users = DB::table('users')->get();

    if ($users->count() > 0) {
        foreach ($users as $user) {
            echo "User data:\n";
            foreach ($user as $key => $value) {
                if ($key !== 'password') {
                    echo "  $key: " . ($value ?? 'NULL') . "\n";
                }
            }
            echo "\n";
        }
    } else {
        echo "No users found\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
