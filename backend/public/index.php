<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true)); //Defines the start time of the application
//Can be used to calculate the execution time/performance of application

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
} //this is just maintenance file when the server is down, this is displayed

require __DIR__.'/../vendor/autoload.php'; //Composer's autoloader

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class); //Kernel is request orchestrator

$response = $kernel->handle( //Handles the incoming request
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
