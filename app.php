<?php
require __DIR__.'/environment.php';
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/Robot/helpers.php';

# Services
$db        = new PDO('sqlite:'.__DIR__.'/database');
$session   = new Robot\Session;
$house     = new Robot\House($db,$session);
$app       = new Slim\App;
$view      = new League\Plates\Engine(__DIR__.'/templates');

$app->get('/all', function($request,$response) use($house) {
    return $response->write( json_encode($house->getAllStatus()) );
})->add(new Robot\Auth);
/**
 * Dashboard route
 */
$app->get('/', function($request,$response) use($view, $house) {
    $data = [
        'message'  => null,
        'shortcuts' => $house->getShortcuts(),
        'rooms' => $house->getRooms(),
        'heating'  => $house->getHeating()
    ];
    return $response->write( $view->render('dashboard',$data) );
})->add(new Robot\Auth);

/**
 * Room display
 */
$app->get('/room/{name}', function ($request, $response, $args) use ($view) {
    $data = [
        'message' => null,
        'title'   => 'Lounge',
        'lights'  => [],
        'climate' => []
    ];
    return $response->write( $view->render('room',$data) );
});
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