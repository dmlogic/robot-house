<?php

function getMiosServer($key) {
    if(defined('MIOS_URL')) {
        return MIOS_URL;
    }

    if($server = Robot\Session::get($key)) {
        return $server;
    }

    $client = new GuzzleHttp\Client;
    $resp =  $client->get('https://sta1.mios.com/locator_json.php?username='.MIOS_UNAME);
    $result = $resp->json();

    if(!is_array($result) || empty($result['units'])) {
        throw new RuntimeException('Could not establish Mios connection');
    }

    $host = null;
    foreach($result['units'] as $vera) {
        if($vera['serialNumber'] != MIOS_VERAID) continue;
        $host = $vera['active_server'];
    }

    if(empty($host)) {
        throw new RuntimeException('Could not establish Mios server');
    }

    $server = sprintf('https://%s/%s/%s/%d/',$host,MIOS_UNAME,MIOS_PWD,MIOS_VERAID);
    Robot\Session::set($key,$server,86400);
    unset($client);

    return $server;
}

function dbQuery($sql) {
    static $DB;
    if ( ! $DB) {
        $DB = new PDO('sqlite:'.BASE_DIR.'database');
        $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $DB->prepare($sql);
}

function dc($value) {
    var_dump($value);
}

function dd($value) {
    dc($value);
    exit;
}