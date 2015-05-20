<?php namespace Robot\Connectors;

use Robot\Collection;

interface Connector {

    public function assignDeviceStates(Collection $devices);

    public function assignSceneStates(Collection $scenes);

    public function setDimmer($id,$value);

    public function setRelay($id,$value);

    public function runScene($number);
}