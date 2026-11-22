<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Setup writable storage directory for Vercel Serverless environment
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Copy SQLite database to persistent writable temp directory on cold start if not exists
$bundledSqlite = __DIR__ . '/../database/database.sqlite';
$writableSqlite = '/tmp/database.sqlite';

if (file_exists($bundledSqlite) && !file_exists($writableSqlite)) {
    copy($bundledSqlite, $writableSqlite);
}

if (file_exists($writableSqlite)) {
    putenv("DB_DATABASE=" . $writableSqlite);
    $_ENV['DB_DATABASE'] = $writableSqlite;
    $_SERVER['DB_DATABASE'] = $writableSqlite;
}

// Require Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle request
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Override storage path for serverless
$app->useStoragePath('/tmp/storage');

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
