<?php
use josegonzalez\Dotenv\Loader as Dotenv;
use Radar\Adr\Boot;
use Zend\Diactoros\Response as Response;
use Zend\Diactoros\ServerRequestFactory as ServerRequestFactory;

/**
 * Bootstrapping
 */
require '../vendor/autoload.php';

Dotenv::load([
    'filepath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env',
    'toEnv' => true,
    'putenv' => true
]);

/*
 * Switch web configs 
 */
$webConfig = (function () {
    $base = '/' . getEnv('USER_PATH');
    if (strpos($_SERVER['REQUEST_URI'], $base) === 0) {
        return 'ConsoleConfig';
    } else {
        return 'StubExecConfig';
    }
})();

if ($_SERVER)

$boot = new Boot();
$adr = $boot->adr([
    'Config', 
    $webConfig
], true);

/**
 * Middleware
 */
// are migrated to WebConfig

/**
 * Routes
 */
// are migrated to WebConfig

/**
 * Run
 */
$adr->run(ServerRequestFactory::fromGlobals(), new Response());
