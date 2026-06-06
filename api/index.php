<?php
define('LARAVEL_START', microtime(true));

$root = dirname(__DIR__);
chdir($root);

require $root . '/vendor/autoload.php';

// Fix: use /tmp for writable cache on Vercel
$app = require_once $root . '/bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

// Create required directories in /tmp
foreach ([
    '/tmp/storage/logs',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/bootstrap/cache',
] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
]);

// Override bootstrap cache path
app()->instance('path.bootstrap', '/tmp/bootstrap');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();
$kernel->terminate($request, $response);