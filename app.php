<?php
define('BASE_DIR',__DIR__.'/');
require BASE_DIR.'environment.php';
require BASE_DIR.'vendor/autoload.php';
require BASE_DIR.'Robot/helpers.php';

# Services
$connector = new Robot\Connectors\Mios( new GuzzleHttp\Client(['base_url' => MIOS_URL]) );
$house     = new Robot\House($connector);
$app       = new Slim\App;
$view      = new League\Plates\Engine(BASE_DIR.'templates');

/**
 * Dashboard route
 */
$app->get('/', function($request,$response) use($view, $house) {

    $data = [
        'message'   => null,
        'rooms'     => $house->getStructure(),
        'scenes'    => $house->getScenes(),
        'shortcuts' => $house->getShortcuts(),
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