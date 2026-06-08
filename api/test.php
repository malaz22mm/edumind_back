<?php
$root = dirname(__DIR__);
chdir($root);

require $root . '/vendor/autoload.php';

try {
    $app = require_once $root . '/bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
    
    // هذا هو السطر اللي يسبب المشكلة غالباً
    $response = $kernel->handle($request);
    
    echo json_encode([
        'status'      => 'Handle OK',
        'http_status' => $response->getStatusCode(),
        'content'     => substr($response->getContent(), 0, 500),
    ]);

} catch (\Throwable $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'class' => get_class($e),
        'file'  => str_replace($root, '', $e->getFile()),
        'line'  => $e->getLine(),
        'trace' => array_slice(array_map(fn($t) => [
            'file'     => str_replace($root ?? '', '', $t['file'] ?? ''),
            'line'     => $t['line'] ?? '',
            'function' => $t['function'] ?? ''
        ], $e->getTrace()), 0, 8)
    ]);
}