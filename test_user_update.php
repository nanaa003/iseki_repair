<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    echo "Before: " . $user->Name_User . "\n";
    $user->Name_User = $user->Name_User . '1';
    $res = $user->save();
    echo "Result: " . ($res ? 'true' : 'false') . "\n";
    echo "After: " . $user->Name_User . "\n";
} else {
    echo "No user found\n";
}
