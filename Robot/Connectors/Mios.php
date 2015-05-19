<?php namespace Robot\Connectors;

use GuzzleHttp\Client;

class Mios implements Connector {

    private $devices;
    private $scenes;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getAllStatus($devices,$scenes) {

        $this->devices = $devices;
        $this->scenes = $scenes;
        $resp =  $this->client->get('data_request?id=lu_status2');
        $raw = $resp->json();
        $this->parseDevices($raw['devices']);
        $this->parseScenes($raw['scenes']);

        return [$this->devices,$this->scenes];
    }

    private function parseScenes($scenes)
    {
        foreach($scenes as $scene) {
            $key = $scene['id'];

            if(!array_key_exists($key, $this->scenes)) {
                continue;
            }

            $this->scenes[$key]->active = (bool) $scene['active'];
        }
    }

    private function parseDevices($devices)
    {
        foreach ($devices as $d) {
            $key = $d['id'];
            if(!array_key_exists($key, $this->devices)) {
                continue;
            }
            switch($this->devices[$key]->type) {
                case 'dimmer':
                    $this->devices[$key]->state = $this->findKeyValue('LoadLevelStatus',$d['states']);
                    break;
                case 'sensor':
                    $this->devices[$key]->battery_level = $this->findKeyValue('BatteryLevel',$d['states']);
                    $this->devices[$key]->is_battery = true;
                    break;
                case 'light':
                    $this->devices[$key]->state = $this->findKeyValue('Status',$d['states']);
                    break;
                case 'rad':
                    $this->devices[$key]->state = $this->findKeyValue('CurrentSetpoint',$d['states']);
                    $this->devices[$key]->battery_level = $this->findKeyValue('BatteryLevel',$d['states']);
                    $this->devices[$key]->is_battery = true;
                    break;
                case 'stat':
                    $this->devices[$key]->state = $this->findKeyValue('CurrentTemperature',$d['states']);
                    $this->devices[$key]->battery_level = $this->findKeyValue('BatteryLevel',$d['states']);
                    $this->devices[$key]->is_battery = true;
                    break;
            }
        }
    }

    private function findKeyValue($key,$haystack)
    {
        foreach($haystack as $stack) {
            if($stack['variable'] == $key) {
                return $stack['value'];
            }
        }
    }

    public function setDimmer($id,$value) {

    }

    public function setRelay($id,$value) {

    }

    public function runScene($number) {

    }
}