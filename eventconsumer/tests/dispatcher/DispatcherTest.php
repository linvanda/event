<?php

namespace member_eventconsumer\tests\dispatcher;

use member_eventconsumer\infrastructure\response\DispatchResponse;
use member_eventlib\event\TestEvent;
use member_eventconsumer\ServiceProvider;
use member_eventlib\tests\TestCase;

class DispatcherTest extends TestCase
{
    public function testDispatch()
    {
        $dis = ServiceProvider::dispatcher();
        $event =  new TestEvent('mysoft', '33', '445', '5555','334dsad');

        $response = new DispatchResponse();
        $dis->dispatch($event, $response);
    }
}
