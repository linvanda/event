<?php

namespace member_eventconsumer\repository;

use member_eventlib\event\IEvent;

abstract class EventRepository
{
    protected $tenant;

    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * 持久化事件对象
     *
     * @param IEvent $event
     * @return void
     */
    abstract public function add(IEvent $event);

    /**
     * 垃圾回收
     *
     * @return void
     */
    abstract public function garbageCollection();

    public function __destruct()
    {
        $this->garbageCollection();
    }
}
