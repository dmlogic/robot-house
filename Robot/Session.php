<?php namespace Robot;

use Memcached;

class Session {

    protected $memcached;

    public function __construct()
    {
        $this->memcached = new Memcached;
        $this->memcached->addServer(MEMCACHED_HOST,MEMCACHED_PORT);
    }

    public function set($key,$value)
    {
        $expire = 60*60*24*29;
        if(!$this->memcached->set( $key , $value , $expire ) ) {
            throw new \RuntimeException("Cannot save session");
        }
    }

    public function get($key)
    {
        return $this->memcached->get($key);
    }
}