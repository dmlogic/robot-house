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

    $data = $house->dashData();
    $data['message'] = null;

    return $response->write( $view->render('dashboard',$data) );
})->add(new Robot\Auth);

$app->get('/refresh', function($request,$response) use($house) {
    return $response->write( json_encode( $house->dashData() ) )->withHeader('Content-type','application/json');

})->add(new Robot\Auth);

$app->get('/debug', function($request,$response) use($connector) {
});

/**
 * Run a Scene
 */
$app->post('/run-scene', function($request,$response) use($connector, $house) {
    if(!$connector->runScene($request->getParam('scene'))) {
        return $response->withStatus(400);
    }

    return $response->write( json_encode( $house->dashData() ) )->withHeader('Content-type','application/json');

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