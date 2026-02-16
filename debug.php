<?php
// debug.php
require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Clear caches before debugging
exec('php artisan config:clear');
exec('php artisan route:clear');
exec('php artisan cache:clear');
exec('php artisan optimize:clear');

// 1️⃣ Check Kernel.php Middleware
echo "=== Middleware Debug ===\n";
$kernelClass = new ReflectionClass($kernel);
$props = ['middleware', 'middlewareGroups', 'routeMiddleware'];

foreach ($props as $propName) {
    $prop = $kernelClass->getProperty($propName);
    $prop->setAccessible(true);
    $value = $prop->getValue($kernel);
    echo "\n-- $propName --\n";
    foreach ($value as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $item) {
                if (!class_exists($item) && !is_string($item)) {
                    echo "[ERROR] Invalid middleware: ";
                    var_dump($item);
                }
            }
        } elseif (!class_exists($v) && !is_string($v)) {
            echo "[ERROR] Invalid middleware: $k => ";
            var_dump($v);
        }
    }
}

// 2️⃣ Check Routes
echo "\n=== Routes Debug ===\n";
try {
    $routes = Route::getRoutes();
    foreach ($routes as $route) {
        $methods = implode(',', $route->methods());
        $uri = $route->uri();
        $action = $route->getActionName();
        echo "[ROUTE] {$methods} {$uri} => {$action}\n";
    }
} catch (Throwable $e) {
    echo "[ERROR] Route parsing failed: ".$e->getMessage()."\n";
}

// 3️⃣ Check PSR-4 Autoloading
echo "\n=== PSR-4 Autoload Debug ===\n";
$files = File::allFiles(app_path());
foreach ($files as $file) {
    $contents = File::get($file);
    preg_match('/namespace\s+([^;]+);/', $contents, $nsMatch);
    preg_match('/class\s+([^\s]+)/', $contents, $classMatch);
    if (!empty($nsMatch) && !empty($classMatch)) {
        $className = $nsMatch[1] . '\\' . $classMatch[1];
        if (!class_exists($className)) {
            echo "[ERROR] Autoload class missing: $className (File: {$file})\n";
        }
    }
}

echo "\n=== Debug Complete ===\n";
