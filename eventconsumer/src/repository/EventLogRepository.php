<?php

namespace member_eventconsumer\repository;

use member_eventlib\event\IEvent;

abstract class EventLogRepository
{
    protected $tenant;

    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * @param IEvent $event
     * @param string $subscriberId
     * @param string $msg
     * @param string $result
     * @return void
     */
    abstract public function log(IEvent $event, $subscriberId, $msg, $result = 'success');

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
