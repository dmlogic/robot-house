<?php

function dc($value) {
    var_dump($value);
}

function dd($value) {
    dc($value);
    exit;
}