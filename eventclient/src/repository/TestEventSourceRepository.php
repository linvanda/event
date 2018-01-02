<?php

namespace member_eventclient\repository;

use member_eventclient\source\EventSource;

class TestEventSourceRepository implements IEventSourceRepository
{
    public function all()
    {
        $source1 = new EventSource('1234', 'test source', 'test source desc', 'testflag');

        return ['testflag' => $source1];
    }
}
