<?php
define('BASE_DIR',__DIR__.'/');
define('ASSETS_VERSION','9489f56');

require BASE_DIR.'environment.php';
require BASE_DIR.'vendor/autoload.php';
require BASE_DIR.'Robot/helpers.php';

# Services
$connector = new Robot\Connectors\Mios( new GuzzleHttp\Client(['base_url' => getMiosServer('robot-mios-server')]) );
$house     = new Robot\House($connector);
$app       = new Slim\App;
$view      = new League\Plates\Engine(BASE_DIR.'templates');

/**
 * Dashboard route
 */
$app->get('/', function($request,$response) use($view, $house) {

    $data = $house->dashData();
    return $response->write( $view->render('dashboard',$data) );

})->add(new Robot\Auth);

/**
 * Refresh data (forces cache reload)
 */
$app->get('/refresh', function($request,$response) use($house) {

    return $response->write( json_encode( $house->dashData(false) ) )->withHeader('Content-type','application/json');

})->add(new Robot\Auth);

/**
 * Manually clear caches in emergency
 */
$app->get('/clear-cache', function($request,$response) {
    Robot\Session::delete('robot-mios-server');
    Robot\Session::delete('robot-house');
    return $response->write('ok');
})->add(new Robot\Auth);

/**
 * Set a device
 */
$app->post('/set-device', function($request,$response) use($connector, $house) {

    $result = $connector->setDevice($request->getParam('id'),$request->getParam('type'),$request->getParam('value'));
    if($result === false) {
        return $response->withStatus(400);
    }
    return $response->write( 'ok' );

})->add(new Robot\Auth);
/**
 * Run a Scene
 */
$app->post('/run-scene', function($request,$response) use($connector, $house) {
    if(!$connector->runScene($request->getParam('scene'))) {
        return $response->withStatus(400);
    }

    return $response->write( json_encode( $house->dashData(false) ) )->withHeader('Content-type','application/json');

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