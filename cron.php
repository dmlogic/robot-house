<?php
/**
 * All this does is refresh the cache
 */
define('BASE_DIR',__DIR__.'/');

require BASE_DIR.'environment.php';
require BASE_DIR.'vendor/autoload.php';
require BASE_DIR.'Robot/helpers.php';

$connector = new Robot\Connectors\Mios( new GuzzleHttp\Client(['base_url' => MIOS_URL]) );
$house     = new Robot\House($connector);

Robot\Session::delete('robot-house');
$house->dashData(false);
getMiosServer('robot-mios-server');