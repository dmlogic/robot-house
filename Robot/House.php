<?php namespace Robot;

use GuzzleHttp\Client;
use Robot\Connectors\Mios;

class House {

    private $db;
    private $session;
    private $connector;

    private $scenes = [];
    private $devices = [];

    private $expires;

    public function __construct($db,$session)
    {
        $this->db = $db;
        $this->session = $session;
        $this->connect();

        $this->expires = 60*5;

        $this->loadElements();
    }

    public function getShortcuts()
    {
        $query = $this->db->prepare("SELECT * FROM shortcuts WHERE type = 'shortcut' ORDER BY sort_order ASC");
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRooms()
    {
        $query = $this->db->prepare("SELECT r.*
                                     FROM shortcuts s
                                     JOIN rooms r ON r.slug = s.room_slug
                                     WHERE s.type = 'room'
                                     ORDER BY s.sort_order ASC");
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getHeating()
    {
        $query = $this->db->prepare("SELECT * FROM shortcuts WHERE type = 'heating' ORDER BY sort_order ASC");
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllStatus()
    {
        if(!$this->session->get('devicesCurrent')) {
            $this->loadStatusFromConnector();
            $this->session->set('devicesCurrent',"yes",$this->expires);
        }

        return ['devices' => $this->devices, 'scenes' => $this->scenes];
    }

    public function getOneStatus($id)
    {
        if(!array_key_exists($id, $this->devices)) {
            return;
        }

        $this->getAllStatus();

        return $this->devices[$id];
    }

    private function connect()
    {
        $this->connector = new Mios( new Client(['base_url' => MIOS_URL]) );
    }

    private function loadStatusFromConnector() {

        list($this->devices,$this->scenes) = $this->connector->getAllStatus($this->devices,$this->scenes);
        $this->session->set('devices',$this->devices,$this->expires);
        $this->session->set('scenes',$this->scenes,$this->expires);
    }

    private function loadElements()
    {
        $this->devices = $this->session->get('devices');
        $this->scenes = $this->session->get('scenes');

        if(!$this->devices || !$this->scenes) {
            $this->loadElementsFromDb();
            $this->session->set('devices',$this->devices,$this->expires);
            $this->session->set('scenes',$this->scenes,$this->expires);
        }
    }

    private function loadElementsFromDb()
    {
        $sql = 'SELECT * FROM devices';
        $query = $this->db->prepare($sql);
        $query->execute();

        foreach($query->fetchAll(\PDO::FETCH_CLASS) as $d) {
            $this->devices[ (int) $d->device_id] = $d;
        }

        $sql = 'SELECT * FROM scenes';
        $query = $this->db->prepare($sql);
        $query->execute();

        foreach($query->fetchAll(\PDO::FETCH_CLASS) as $s) {
            $this->scenes[$s->number] = $s;
        }
    }



}