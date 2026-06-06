<?php
$root = dirname(__DIR__);
chdir($root);

require $root . '/vendor/autoload.php';

// Force stderr logging before bootstrap
putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = 'stderr';
$_SERVER['LOG_CHANNEL'] = 'stderr';

try {
    $app = require_once $root . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    
    echo json_encode([
        'status' => 'OK',
        'http_status' => $response->getStatusCode(),
        'content' => substr($response->getContent(), 0, 300),
    ]);

} catch (\Throwable $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'class' => get_class($e),
        'file'  => str_replace($root, '', $e->getFile()),
        'line'  => $e->getLine(),
    ]);
}