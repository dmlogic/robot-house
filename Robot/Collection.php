<?php namespace Robot;

class Collection extends \Slim\Http\Collection {

    public function __set($key,$value)
    {
        $this->set($key,$value);
    }

    public function __get($key)
    {
        return $this->get($key);
    }
}