<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing User Authentication...\n\n";

try {
    // Find admin user
    $user = DB::table('users')->where('email', 'admin')->first();

    if ($user) {
        echo "User found:\n";
        echo "  ID: " . $user->id . "\n";
        echo "  Email: " . $user->email . "\n";
        echo "  Name: " . $user->name . "\n";
        echo "  Is Admin: " . ($user->is_admin ? 'YES' : 'NO') . "\n";
        echo "  Balance: " . $user->balance . "\n\n";

        // Try to authenticate
        if (Hash::check('admin123', $user->password)) {
            echo "Password verification: SUCCESS\n";
        } else {
            echo "Password verification: FAILED\n";
        }
    } else {
        echo "User 'admin' NOT FOUND\n\n";
        echo "Existing users:\n";
        $users = DB::table('users')->select('id', 'email', 'name')->get();
        foreach ($users as $u) {
            echo "  - " . $u->email . " (" . $u->name . ")\n";
        }
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
