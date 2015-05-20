<?php namespace Robot\Connectors;

use Robot\Collection;

interface Connector {

    public function assignDeviceStates(Collection $devices);

    public function assignSceneStates(Collection $scenes);

    public function setDevice($id,$type,$value);

    public function runScene($number);
}