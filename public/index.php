<?php

use App\Container;
use App\Kernel;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$container  = new Container();
$router     = new Router();
$kernel     = new Kernel($container, $router);
$uri        = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$httpMethod = $_SERVER['REQUEST_METHOD'];

$kernel->handleRequest($uri, $httpMethod);
