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

$boot = new Boot();
$adr = $boot->adr([
    'Config', 
    'WebConfig'
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
