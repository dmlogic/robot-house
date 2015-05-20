<?php

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