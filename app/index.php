<?php
/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../logs/error.log');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/checkSetup.php';
require __DIR__ . '/../src/tools.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

$logger = new Logger('PosibrainRestApi');
if (! is_dir(__DIR__ . '/../logs/')) {
    mkdir(__DIR__ . '/../logs/');
    chmod(__DIR__ . '/../logs/', '755');
}
$loggerHandler = new RotatingFileHandler(__DIR__ . '/../logs/restapi.log', 2, Logger::DEBUG);
$logger->pushHandler($loggerHandler);

$request = Request::createFromGlobals();

$routes = new Routing\RouteCollection();
$routes->add('bots', new Routing\Route('/bots/{botId}', array(
    'botId' => '',
    '_controller' => 'Posibrain\\RestApi\\RestApiController::actionGetBots'
), array(
    '_method' => 'GET'
)));
$routes->add('positrons', new Routing\Route('/positrons', array(
    '_controller' => 'Posibrain\\RestApi\\RestApiController::actionGetPositrons'
), array(
    '_method' => 'GET'
)));
$routes->add('submit', new Routing\Route('/submit/{botId}/{botLang}', array(
    'botId' => '',
    'botLang' => '',
    '_controller' => 'Posibrain\\RestApi\\RestApiController::actionSubmit'
), array(
    '_method' => 'GET'
)));

$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);
$resolver = new ControllerResolver();
$response = null;
try {
    $request->attributes->add($matcher->match($request->getPathInfo()));
    $controller = $resolver->getController($request);
    $arguments = $resolver->getArguments($request, $controller);
    $response = call_user_func_array($controller, $arguments);
} catch (ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (\Exception $e) {
    $logger->addCritical('[500] An error occurred', $e);
    $response = new Response('An error occurred', 500);
}
$response->send();