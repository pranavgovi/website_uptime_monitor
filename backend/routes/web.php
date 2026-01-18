<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    $htmlPath = public_path('index.html');
    if (file_exists($htmlPath)) {
        return response(file_get_contents($htmlPath))->header('Content-Type', 'text/html');
    }
    return response('Frontend not found. Please run: npm run build', 404);
})->where('any', '.*');
