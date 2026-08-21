<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$profiles = App\Models\DeptAuthorityProfile::with(['user', 'department'])->get();
foreach ($profiles as $p) {
    echo "User: {$p->user->email} ({$p->user->role}) -> Department: {$p->department->code} ({$p->department->name})\n";
}
