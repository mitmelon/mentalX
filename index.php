<?php
/**
 * @docs   https://manomite.net/docs
 *
 * @version    1.0.0
 * @author     Manomite Limited
 *
 * @copyright  2024 Manomite Limited
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/autoload.php';

////////////////////////////////////////////////////////////////INDEX ERROR LOGGER//////////////////////////////////////////////////////
error_reporting(E_ALL);
ini_set('display_errors', false);
ini_set('log_errors', true);
ini_set('error_log', SYSTEM_DIR . '/errors/index.log');
ini_set('log_errors_max_len', 1024);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], (strlen('/mentalx')));
try {

    $app = AppFactory::create();

    $app->get('/', function (Request $request, Response $response) {
       
        $html = "am working";
        $response->getBody()->write($html);
        return $response;
    });
    $app->get('/index', function (Request $request, Response $response) {
        $html = "am working";
        $response->getBody()->write($html);
        return $response;
    });
    
    $app->run();
} catch (Throwable $e) {
    echo $e;
    http_response_code(400);
}