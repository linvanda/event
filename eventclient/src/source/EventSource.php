<?php

namespace member_eventclient\source;

use member_eventclient\ServiceProvider;

/**
 * 事件源
 *
 * Class EventSource
 * @package member_eventclient\source
 */
class EventSource
{
    protected $id;
    protected $name;
    protected $desc;
    protected $flag;

    public function __construct($id, $name, $desc, $flag)
    {
        $this->id = $id;
        $this->name = $name;
        $this->desc = $desc;
        $this->flag = $flag;
    }

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }

    public function desc()
    {
        return $this->desc;
    }

    public function flag()
    {
        return $this->flag;
    }

    /**
     * 根据flag获取对应源的id
     *
     * @param $flag
     * @return string
     */
    public static function idOfFlag($flag)
    {
        $all = ServiceProvider::sourceRepository()->all();

        if (is_array($all) && array_key_exists($flag, $all)) {
            return $all[$flag]->id();
        }

        return '';
    }
}
