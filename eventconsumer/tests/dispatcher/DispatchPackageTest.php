<?php

namespace member_eventconsumer\tests\dispatcher;

use member_eventconsumer\dispatcher\DispatchPackage;
use member_eventlib\event\TestEvent;
use member_eventlib\tests\TestCase;

class DispatchPackageTest extends TestCase
{
    public function testSend()
    {
        $package = new DispatchPackage(new TestEvent('mysoft', '33', '445', '5555','334dsad'), '12345');

        $package->send();
    }
}
