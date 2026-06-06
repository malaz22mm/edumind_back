<?php
$root = dirname(__DIR__);
chdir($root);

// Test 1: vendor exists?
if (!file_exists($root . '/vendor/autoload.php')) {
    die(json_encode(['error' => 'No vendor folder', 'root' => $root]));
}

require $root . '/vendor/autoload.php';

// Test 2: bootstrap works?
try {
    $app = require_once $root . '/bootstrap/app.php';
    echo json_encode(['status' => 'Laravel bootstrapped OK', 'root' => $root]);
} catch (\Throwable $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}