<?php

namespace member_eventconsumer\repository;

use member_eventlib\event\IEvent;

class TestEventRepository extends EventRepository
{
    public function add(IEvent $event)
    {

    }

    public function garbageCollection()
    {

    }
}
