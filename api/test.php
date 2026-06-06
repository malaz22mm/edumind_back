<?php
$root = dirname(__DIR__);
chdir($root);

require $root . '/vendor/autoload.php';

try {
    $app = require_once $root . '/bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $request = Illuminate\Http\Request::capture();
    
    echo json_encode([
        'status' => 'Kernel created OK',
        'request_path' => $request->path(),
        'request_method' => $request->method(),
    ]);
    
} catch (\Throwable $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file'  => str_replace($root, '', $e->getFile()),
        'line'  => $e->getLine(),
        'trace' => array_slice(array_map(fn($t) => [
            'file' => str_replace($root, '', $t['file'] ?? ''),
            'line' => $t['line'] ?? '',
            'function' => $t['function'] ?? ''
        ], $e->getTrace()), 0, 5)
    ]);
}