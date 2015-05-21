<?php namespace Robot;

use Memcached;
use Pimple\ServiceProviderInterface;

class Session implements ServiceProviderInterface {

    protected $memcached;

    protected static $session;

    public function __construct()
    {
        $this->memcached = new Memcached;
        $this->memcached->addServer(MEMCACHED_HOST,MEMCACHED_PORT);
    }

    public function register(\Pimple\Container $container)
    {
        $container['session'] = $this;
    }

    protected static function instance()
    {
        if(!static::$session) {
            static::$session = new static;
        }

        return static::$session;
    }

    public static function set($key,$value,$expire = null)
    {
        if(!$expire) {
            $expire = 60*60*24*29;
        }
        if(!static::instance()->memcached->set( $key , $value , $expire ) ) {
            throw new \RuntimeException("Cannot save session");
        }
    }

    public static function get($key)
    {
        return static::instance()->memcached->get($key);
    }

    public function delete($key)
    {
        static::instance()->memcached->delete($key);
    }
}