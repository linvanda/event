<?php

namespace member_eventconsumer\repository;

use member_eventlib\event\IEvent;

class TestEventLogRepository extends EventLogRepository
{
    public function log(IEvent $event, $subscriberId, $msg, $result = 'success')
    {

    }

    public function garbageCollection()
    {

    }
}
