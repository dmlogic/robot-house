<?php

use Phinx\Migration\AbstractMigration;

class CreateTables extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('rooms');
        $table->addColumn('slug', 'string')
              ->addColumn('name', 'string')
              ->addIndex(['slug'], ['unique' => true])
              ->create();

        $table = $this->table('devices');
        $table->addColumn('device_id', 'integer')
              ->addColumn('room', 'string')
              ->addColumn('name', 'string')
              ->addColumn('type', 'string')
              ->addColumn('state', 'string',['null' => true])
              ->addColumn('is_battery', 'integer',['default' => 0])
              ->addColumn('battery_level', 'integer',['null' => true])
              ->addIndex(['device_id'], ['unique' => true])
              ->create();

        $table = $this->table('scenes');
        $table->addColumn('number', 'integer')
              ->addColumn('name', 'string')
              ->addColumn('active', 'integer',['default' => 0])
              ->addIndex(['number'], ['unique' => true])
              ->create();

        $table = $this->table('shortcuts');
        $table->addColumn('scene_number', 'integer',['null' => true])
              ->addColumn('room_slug', 'integer',['null' => true])
              ->addColumn('type', 'string')
              ->addColumn('sort_order', 'integer')
              ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('rooms');
        $this->dropTable('devices');
        $this->dropTable('scenes');
        $this->dropTable('shortcuts');
    }
}