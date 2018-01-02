<?php

namespace member_eventconsumer\tests\schedular;

use member_eventlib\event\TestEvent;
use member_eventconsumer\Schedular;
use member_eventlib\tests\TestCase;

class SchedularTest extends TestCase
{
    public function testRun()
    {
        $schedular = Schedular::create();
        $package =  new TestEvent('mysoft', '33', '445', '5555','334dsad');
        $schedular->run($package);
    }
}
