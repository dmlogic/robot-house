<?php namespace Robot;
/**
 * Get/Set all data about a House
 */
use Robot\Session;
use Robot\Collection;
use Robot\Connectors\Connector;

class House {

    /**
     * All the rooms in the house
     *
     * @var array
     */
    private $rooms;

    /**
     * All the devices in the house
     *
     * @var Robot\Collection
     */
    private $devices;

    /**
     * All the scenes in the house
     *
     * @var Robot\Collection
     */
    private $scenes;

    /**
     * All the shorcuts for the Dashboard
     *
     * @var Robot\Collection
     */
    private $shorcuts;

    /**
     * The connection to HA data source
     *
     * @var Robot\Connectors\Connector
     */
    private $connector;

    /**
     * How long to hang on to device states
     *
     * @var integer
     */
    private $expire;

    /**
     * The ID of the Boost hander
     * @var int
     */
    private $boosterId;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
        $this->rooms     = [];
        $this->devices   = new Collection;
        $this->scenes    = new Collection;
    }

    public function dashData($fromCache = true)
    {
        if($fromCache && $data = Session::get('robot-house')) {
            return $data;
        }


        $data = [
            'rooms'     => $this->getStructure(),
            'shortcuts' => $this->getShortcuts(),
            'temps'     => $this->getTemperatures(),
            'booster'   => $this->boosterId,
        ];
        Session::set('robot-house',$data);
        return $data;
    }

    /**
     * Get the structure of the house as rooms and contained devices
     *
     * @return Robot\Collection
     */
    private function getStructure()
    {
        $this->lookupRoomsAndDevices();
        $this->setDeviceStates();

        $out = $this->rooms;
        foreach ($this->devices as $key => $device) {
            $out[$device->room]['devices'][$key] = $device;
        }

        return $out;
    }

    /**
     * Get all the scenes for the house
     *
     * @return Robot\Collection
     */
    private function getScenes()
    {
        $this->lookupScenes();
        $this->setSceneStates();

        return $this->scenes;
    }

    private function getShortcuts()
    {
        $this->getScenes();

        $sql = 'SELECT
                    s.*,
                    r.name room_name,
                    c.name scene_name
                FROM shortcuts s
                LEFT JOIN rooms r ON s.room_slug = r.slug
                LEFT JOIN scenes c ON s.scene_number = c.number
                ORDER BY s.sort_order ASC';
        $query = dbQuery($sql);
        $query->execute();

        $out = [];

        foreach($query->fetchAll(\PDO::FETCH_CLASS) as $row) {
            $row->active = false;
            if($row->type != 'room') {
                $row->active = $this->scenes[$row->scene_number]->active;
            }
            $out[$row->type][] = $row;
        }

        return $out;
    }

    private function lookupScenes()
    {
        $sql = 'SELECT * FROM scenes';
        $query = dbQuery($sql);
        $query->execute();

        foreach($query->fetchAll(\PDO::FETCH_CLASS) as $row) {
            $this->scenes->set($row->number, $row);
        }
    }

    /**
     * Assign a current state to all devices from the Connector
     */
    private function setDeviceStates()
    {
        $this->devices = $this->connector->assignDeviceStates($this->devices);
    }

    /**
     * Assign a current state to all scenes from the Connector
     *
     * @todo - Apply a fix for Hot Water Boost
     */
    private function setSceneStates()
    {
        $this->scenes = $this->connector->assignSceneStates($this->scenes);
        $this->revoltingHotWaterFix();
    }

    /**
     * I hate this, but the data is junk
     *
     * @return void
     */
    private function revoltingHotWaterFix()
    {
        $hotWaterScene = 2;
        $hotWaterDevice = 34;

        if($this->devices[$hotWaterDevice]->state == 'Off') {
            $this->scenes[$hotWaterScene]->active = false;
        } else {
            $this->scenes[$hotWaterScene]->active = true;
        }
    }

    /**
     * Get all our rooms and devices
     *
     * @return void
     */
    private function lookupRoomsAndDevices()
    {
        $sql = 'SELECT d.device_id,
                d.room,
                d.name,
                d.type,
                d.state,
                d.is_battery,
                d.battery_level,
                d.style_class,
                r.name room_name
              FROM devices d
              LEFT JOIN rooms r on d.room = r.slug
              GROUP BY d.device_id';
            $query = dbQuery($sql);
            $query->execute();

        foreach($query->fetchAll(\PDO::FETCH_CLASS) as $row) {
            if(!array_key_exists($row->room, $this->rooms) ) {
                $this->rooms[$row->room] = ['name' => $row->room_name, 'devices' => [] ];
            }
            if($row->type === 'boost') {
                $this->boosterId = $row->device_id;
            }
            $this->devices->set($row->device_id, $row);
        }
    }

    /**
     * Get the temperature from the main wall stat
     *
     * @return array
     */
    protected function getTemperatures()
    {
        $out = [
            'target'  => 0,
            'current' => 0
        ];

        if($data = $this->devices->get(STAT_ID)) {
            $out['target'] = $data->state;
            $out['current'] = $data->current;
        }

        return $out;
    }
}