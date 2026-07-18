<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MbkmProgram;

$programs = MbkmProgram::all();
foreach ($programs as $p) {
    echo "ID: {$p->id} | Title: {$p->title} | Status: {$p->status}\n";
}
