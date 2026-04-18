<?php

// Script to audit Laravel routes vs Controller methods
// Usage: php audit_routes.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Container\Container;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$routes = Route::getRoutes();
$errors = [];

foreach ($routes as $route) {
    $action = $route->getAction();
    
    if (isset($action['controller'])) {
        $controllerAction = $action['controller'];
        if (str_contains($controllerAction, '@')) {
            [$controller, $method] = explode('@', $controllerAction);
            
            if (!class_exists($controller)) {
                $errors[] = "[MISSING CLASS] Controller $controller does not exist (Route: {$route->uri()})";
                continue;
            }
            
            if (!method_exists($controller, $method)) {
                $errors[] = "[MISSING METHOD] Method $method does not exist in $controller (Route: {$route->uri()})";
            }
        }
    }
}

if (empty($errors)) {
    echo "✅ No errors found in route/controller mapping.\n";
} else {
    echo "❌ Errors found:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}
