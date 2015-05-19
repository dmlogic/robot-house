<?php namespace Robot;

use Memcached;
use Pimple\ServiceProviderInterface;

class Session implements ServiceProviderInterface {

    protected $memcached;

    public function __construct()
    {
        $this->memcached = new Memcached;
        $this->memcached->addServer(MEMCACHED_HOST,MEMCACHED_PORT);
    }

    public function register(\Pimple\Container $container)
    {
        $container['session'] = $this;
    }

    public function set($key,$value,$expire = null)
    {
        if(!$expire) {
            $expire = 60*60*24*29;
        }
        if(!$this->memcached->set( $key , $value , $expire ) ) {
            throw new \RuntimeException("Cannot save session");
        }
    }

    public function get($key)
    {
        return $this->memcached->get($key);
    }
}