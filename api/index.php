<?php

// TEMPORARY DEBUG - REMOVE AFTER FIXING
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('LARAVEL_START', microtime(true));

$root = dirname(__DIR__);
chdir($root);

// Check files exist
if (!file_exists($root . '/vendor/autoload.php')) {
    die(json_encode(['error' => 'vendor/autoload.php not found', 'root' => $root]));
}

if (!file_exists($root . '/bootstrap/app.php')) {
    die(json_encode(['error' => 'bootstrap/app.php not found', 'root' => $root]));
}

require $root . '/vendor/autoload.php';

$app = require_once $root . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);