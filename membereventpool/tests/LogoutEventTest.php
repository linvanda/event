<?php

namespace member_eventpool\tests;

use member_eventlib\tests\TestCase;
use member_eventpool\LogoutEvent;

class LogoutEventTest extends TestCase
{
    public function testInstance()
    {
        $e = new LogoutEvent('retesting', '1232345454334', '2332r3232', 'qw2123eee');
        $this->assertEquals($e->source(), LogoutEvent::DEFAULT_SOURCE);
    }
}
