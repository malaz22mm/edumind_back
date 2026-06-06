<?php

/**
 * Vercel serverless entry point.
 * Forwards all HTTP requests to Laravel's public front controller.
 */
chdir(dirname(__DIR__));

require __DIR__.'/../public/index.php';
