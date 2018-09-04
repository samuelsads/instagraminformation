<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;


$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/{username}', function (Request $request, Response $response, array $args) {
    $username = $args['username'];
    $source = file_get_contents("https://www.instagram.com/".$username);

    preg_match('/<script type="text\/javascript">window\._sharedData =([^;]+);<\/script>/', $source, $matches);

    if (!isset($matches[1]))
        return false;

    $r = $matches[1];
    $r = json_decode($r,true);
    $r  = $r['entry_data'];

    return $response->withJson($r['ProfilePage'][0]);
});

$app->run();