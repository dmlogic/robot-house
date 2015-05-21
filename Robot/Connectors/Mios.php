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
                    $devices[$key]->wakeup = ceil( $this->findKeyValue('WakeupInterval',$d['states']) / 60 );
                    break;
                case 'stat':
                    $devices[$key]->current = (int) $this->findKeyValue('CurrentTemperature',$d['states']);
                    $devices[$key]->state = (int) $this->findKeyValue('CurrentSetpoint',$d['states']);
                    $devices[$key]->battery_level = (int) $this->findKeyValue('BatteryLevel',$d['states']);
                    $devices[$key]->is_battery = true;
                    break;
                case 'hvac':
                    $devices[$key]->state = $this->findKeyValue('ModeStatus',$d['states']);
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

    /**
     * Set the appropriate var as per capabiities established here:
     * http://IP:3480/data_request?id=invoke&DeviceNum=ID
     *
     * @param int $id
     * @param string $type
     * @param mixed $value
     */
    public function setDevice($id,$type,$value) {

        $action = 'data_request?id=action&output_format=json&DeviceNum=' . (int) $id;

        switch($type) {
            case 'dimmer':
                $action .= '&serviceId=urn:upnp-org:serviceId:Dimming1&action=SetLoadLevelTarget&newLoadlevelTarget='.(int) $value;
                break;
            case 'light':
                $target = ($value) ? 1 : 0;
                $action .= '&serviceId=urn:upnp-org:serviceId:SwitchPower1&action=SetTarget&newTargetValue=' . $target;
                break;
            case 'rad':
            case 'stat':
                $action .= '&serviceId=urn:upnp-org:serviceId:TemperatureSetpoint1_Heat&action=SetCurrentSetpoint&NewCurrentSetpoint=' .(int) $value;
                break;
            case 'hvac':
                $target = ($value) ? 'HeatOn' : 'Off';
                $action .= '&serviceId=urn:upnp-org:serviceId:HVAC_UserOperatingMode1&action=SetModeTarget&NewModeTarget='.$target ;
                break;
        }

        try {
            $resp =  $this->client->get($action);
            $raw = $resp->json();
            return $this->handleJobResponse($resp->json());

        } catch(\Exception $e) {
            return false;
        }
    }

    private function handleJobResponse($resp)
    {
        $resp = reset($resp);
        if(!array_key_exists('JobID', $resp)) {
            return false;
        }

        return true;

        // We're not going to the below.
        // It comes back pending so often that we may as well respond quickly
        // and then do a refresh from the front end. By the time that's happened
        // the pending state has gone

        // Vera needs a little time to digest this
        sleep(1);

        // Now we need another to call to see what happened
        try {
            $action = 'data_request?id=jobstatus&output_format=json&plugin=zwave&&job='.$resp['JobID'];
            $resp =  $this->client->get($action);
            $result = $resp->json();
        } catch(\Exception $e) {
            return false;
        }

        switch($result['status']) {
            // The job failed
            case 2:
            case 3:
            case 6:
                return false;
                break;
            // The job is done
            case -1:
            case 4:
                return true;
                break;
            // Anything else means it's pending on wake-up
            default:
                return 'pending';
        }
    }

    public function runScene($number) {
        try {
            $resp =  $this->client->get('data_request?id=lu_action&serviceId=urn:micasaverde-com:serviceId:HomeAutomationGateway1&output_format=json&action=RunScene&SceneNum='.(int) $number);
            $raw = $resp->json();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}