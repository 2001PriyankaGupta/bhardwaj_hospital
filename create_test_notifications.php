<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;

$users = User::where('user_type', 'patient')->get();
foreach($users as $u) {
    Notification::create([
        'user_id' => $u->id,
        'type' => 'test',
        'title' => 'System Update',
        'meta_data' => ['message' => 'Hello ' . $u->name . ', your notification system is now active!']
    ]);
}
echo "Created notifications for " . $users->count() . " users.\n";
