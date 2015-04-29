<?php
require __DIR__.'/environment.php';
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/Robot/helpers.php';

session_start();

$app = new \Slim\App;
$auth = new Robot\Auth($app['request'],$app['response']);
$view = new League\Plates\Engine(__DIR__.'/templates');

/**
 * Dashboard route
 */
$app->get('/', function($request,$response) use($view) {
    return $response;
})->add(new Robot\Auth);

/**
 * Login form
 */
$app->get('/login', function($request,$response) use($view) {

    switch($request->getParam('reason')) {
        case 1:
            $message = 'You must login to access this site';
            break;
        case 2:
            $message = 'Incorrect login details';
            break;
        default:
            $message = null;
    }

    $response->write( $view->render('login',['message' => $message ]) );
    return $response;
});

/**
 * Login processor
 */
$app->post('/login', function($request,$response) use($auth) {
    return $auth->authenticate($request->getParam('username'),$request->getParam('password'));
});

$app->run();