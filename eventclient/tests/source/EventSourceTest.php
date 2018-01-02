<?php

namespace member_eventlib\tests\event\source;

use \member_eventclient\source\EventSource;
use member_eventlib\tests\TestCase;

class EventSourceTest extends TestCase
{
    public function testId()
    {
        $id = EventSource::idOfFlag('testflag');

        $this->assertTrue($id == '1234');
    }
}
