<?php namespace Robot\Connectors;

interface Connector {

    public function getAllStatus($devices,$scenes);

    public function setDimmer($id,$value);

    public function setRelay($id,$value);

    public function runScene($number);
}