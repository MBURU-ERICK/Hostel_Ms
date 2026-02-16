<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class DebugProject extends Command
{
    protected $signature = 'debug:project';
    protected $description = 'Debug Laravel project for routes, middleware, and PSR-4 autoloading issues';

    public function handle()
    {
        $this->info('=== Middleware Debug ===');
        $kernel = app()->make(\App\Http\Kernel::class);
        $kernelClass = new ReflectionClass($kernel);
        $props = ['middleware', 'middlewareGroups', 'routeMiddleware'];

        foreach ($props as $propName) {
            $prop = $kernelClass->getProperty($propName);
            $prop->setAccessible(true);
            $value = $prop->getValue($kernel);
            $this->line("\n-- $propName --");
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $item) {
                        if (!class_exists($item) && !is_string($item)) {
                            $this->error("[ERROR] Invalid middleware: ".print_r($item, true));
                        }
                    }
                } elseif (!class_exists($v) && !is_string($v)) {
                    $this->error("[ERROR] Invalid middleware: $k => ".print_r($v, true));
                }
            }
        }

        $this->info("\n=== Routes Debug ===");
        try {
            $routes = Route::getRoutes();
            foreach ($routes as $route) {
                $methods = implode(',', $route->methods());
                $uri = $route->uri();
                $action = $route->getActionName();
                $this->line("[ROUTE] {$methods} {$uri} => {$action}");
            }
        } catch (\Throwable $e) {
            $this->error("[ERROR] Route parsing failed: ".$e->getMessage());
        }

        $this->info("\n=== PSR-4 Autoload Debug ===");
        $files = File::allFiles(app_path());
        foreach ($files as $file) {
            $contents = File::get($file);
            preg_match('/namespace\s+([^;]+);/', $contents, $nsMatch);
            preg_match('/class\s+([^\s]+)/', $contents, $classMatch);
            if (!empty($nsMatch) && !empty($classMatch)) {
                $className = $nsMatch[1] . '\\' . $classMatch[1];
                if (!class_exists($className)) {
                    $this->error("[ERROR] Autoload class missing: $className (File: {$file})");
                }
            }
        }

        $this->info("\n=== Debug Complete ===");
    }
}
