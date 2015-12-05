<?php

use Phinx\Migration\AbstractMigration;

class SeedData extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->seedRooms();
        $this->seedDevices();
        $this->seedScenes();
        $this->seedShortcuts();
    }

    /**
     * Create a CSV with these:
     * scene_number,room_slug,type,sort_order
     *
     * @return void
     */
    public function seedShortcuts()
    {
        $contents = $this->readCsv('shortcuts',true);
        foreach($contents as $row) {
            $sql = sprintf("INSERT INTO shortcuts(scene_number,room_slug,type,sort_order) VALUES('%d','%s','%s',%d)",$row[0],$row[1],$row[2],$row[3]);
            $res = $this->execute($sql);
        }
    }

    /**
     * Create a CSV with these:
     * number,name
     *
     * @return void
     */
    public function seedScenes()
    {
        $contents = $this->readCsv('scenes');
        foreach($contents as $row) {
            $sql = sprintf("INSERT INTO scenes(`number`,`name`) VALUES('%d','%s')",$row[0],$row[1]);
            $res = $this->execute($sql);
        }
    }

    /**
     * Create a CSV with these:
     * slug,name
     *
     * @return void
     */
    private function seedRooms()
    {
        $contents = $this->readCsv('rooms');
        foreach($contents as $row) {
            $sql = sprintf("INSERT INTO rooms(slug,name) VALUES('%s','%s')",$row[0],$row[1]);
            $res = $this->execute($sql);
        }
     }

    /**
     * Create a CSV with these:
     * device_id,room,name,type,state,is_battery,battery_level
     *
     * Note state is not entered
     *
     * @return void
     */
    private function seedDevices()
    {
        $contents = $this->readCsv('devices',true);
        foreach($contents as $row) {
            $id = $row[0];
            $room = $row[1];
            $name = $row[2];
            $type = $row[3];
            $bat = (int) $row[5];
            $lev = (int) $row[6];
            $sql = sprintf("INSERT INTO devices(device_id,room,name,type,is_battery,battery_level,style_class)
                                        VALUES(%d,'%s','%s','%s',%d,%d,'%s')",
                                               $id,$room,$name,$type,$bat,$lev,$row[7]);
            $res = $this->execute($sql);
        }
     }

    private function readCsv($filename,$excludeHeader = false)
    {
        $out = [];
        $filename = __DIR__.'/csv/'.$filename.'.csv';
        $row = 0;
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                if($excludeHeader && $row == 1) continue;
                $out[] = $data;
            }
            fclose($handle);
        }

        return $out;;
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('TRUNCATE TABLE rooms');
        $this->execute('TRUNCATE TABLE scenes');
        $this->execute('TRUNCATE TABLE devices');
        $this->execute('TRUNCATE TABLE shortcuts');
    }
}