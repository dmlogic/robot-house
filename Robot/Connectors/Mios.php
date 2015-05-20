<?php namespace Robot\Connectors;
/**
 * Vera Lite connector for Robot House
 */
use Robot\Collection;
use GuzzleHttp\Client;

class Mios implements Connector {

    /**
     * The http client connection
     *
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * Raw data returned on device lookup
     *
     * @var array
     */
    private $raw;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Return a key from raw data
     *
     * @param  mixed $key
     * @return array
     */
    private function get($key) {

        if(!$this->raw) {
            $resp =  $this->client->get('data_request?id=lu_status2');
            $this->raw = $resp->json();
        }

        if(!array_key_exists($key, $this->raw)) {
            throw new \RuntimeException('Invalid key');
        }

        return $this->raw[$key];
    }

    /**
     * Iterate through all scenes and apply state to any present
     * in the supplied Collection
     *
     * @param  Collection $scenes
     * @return Collection
     */
    public function assignSceneStates(Collection $scenes)
    {
        $rawData = $this->get('scenes');

        foreach ($rawData as $s) {
            $key = $s['id'];
            if(!$scenes->has($key)) {
                continue;
            }

            $scenes[$key]->active = (bool) $s['active'];
        }

        return $scenes;
    }

    /**
     * Iterate through all devices and apply state to any present
     * in the supplied Collection
     *
     * @param  Collection $devices
     * @return Collection
     */
    public function assignDeviceStates(Collection $devices)
    {
        $rawData = $this->get('devices');

        foreach ($rawData as $d) {
            $key = $d['id'];
            if(!$devices->has($key)) {
                continue;
            }
            switch($devices[$key]->type) {
                case 'dimmer':
                    $devices[$key]->state = (int) $this->findKeyValue('LoadLevelStatus',$d['states']);
                    break;
                case 'sensor':
                    $devices[$key]->battery_level = (int) $this->findKeyValue('BatteryLevel',$d['states']);
                    $devices[$key]->is_battery = true;
                    break;
                case 'light':
                    $devices[$key]->state = $this->findKeyValue('Status',$d['states']);
                    break;
                case 'rad':
                    $devices[$key]->current = null;
                    $devices[$key]->state = (int) $this->findKeyValue('CurrentSetpoint',$d['states']);
                    $devices[$key]->battery_level = (int) $this->findKeyValue('BatteryLevel',$d['states']);
                    $devices[$key]->is_battery = true;
                    break;
                case 'stat':
                    $devices[$key]->current = (int) $this->findKeyValue('CurrentTemperature',$d['states']);
                    $devices[$key]->state = (int) $this->findKeyValue('CurrentSetpoint',$d['states']);
                    $devices[$key]->battery_level = (int) $this->findKeyValue('BatteryLevel',$d['states']);
                    $devices[$key]->is_battery = true;
                    break;
            }
        }

        return $devices;
    }

    /**
     * Find a value for a given key in the raw Vera data
     *
     * @param  mixed $key
     * @param  array $haystack
     * @return mixed
     */
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