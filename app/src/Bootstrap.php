<?php declare(strict_types = 1);

namespace Nelwhix\ContactForm;

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

// Load all the environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set up error page handling
$whoops = new \Whoops\Run;
if ($_ENV['APP_ENV'] !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
        echo "Friendly Error Page";
    });
}

$whoops->register();

// set up request/response object;
$injector = include('Dependencies.php');

$request = $injector->make("Http\HttpRequest");
$response = $injector->make("Http\HttpResponse");

$routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
    $routes = include('Routes.php');

    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};

$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page not found');
        $response->setStatusCode(404);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];

        $class = $injector->make($className);
        $class->$method($vars);
        break;
}



foreach ($response->getHeaders() as $header) {
    header($header, false);
}

echo $response->getContent();