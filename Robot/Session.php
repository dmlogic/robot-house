<?php namespace Robot;

use Memcached;
use FileSystemCache;

class Session {

    protected $memcached;

    protected static $session;

    public function __construct()
    {
        // $this->memcached = new Memcached;
        // $this->memcached->addServer(MEMCACHED_HOST,MEMCACHED_PORT);
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

        $cacheKey = FileSystemCache::generateCacheKey($key);
        return FileSystemCache::store($cacheKey, $value,$expire);

        // if(!static::instance()->memcached->set( $key , $value , $expire ) ) {
        //     throw new \RuntimeException("Cannot save session");
        // }
    }

    public static function get($key)
    {
        $cacheKey = FileSystemCache::generateCacheKey($key);
        return FileSystemCache::retrieve($cacheKey);
        // return static::instance()->memcached->get($key);
    }

    public static function delete($key)
    {
        $cacheKey = FileSystemCache::generateCacheKey($key);
        return FileSystemCache::invalidate($cacheKey);
        // static::instance()->memcached->delete($key);
    }
}