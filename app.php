<?php
require __DIR__.'/environment.php';
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/Robot/helpers.php';

$db = new PDO('sqlite:'.__DIR__.'/database');

$app = new \Slim\App;
$view = new League\Plates\Engine(__DIR__.'/templates');

/**
 * Dashboard route
 */
$app->get('/', function($request,$response) use($view) {
    $data = ['message' => null ];
    return $response->write( $view->render('dashboard',$data) );
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
$app->post('/login', function($request,$response) use($app) {
    $auth = new Robot\Auth($app['request'],$app['response']);
    return $auth->authenticate();
});

$app->run();