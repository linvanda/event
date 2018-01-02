<?php

namespace member_eventconsumer\tests\subscriber\middleware;

use member_eventconsumer\subscriber\SubscriberEventWrapper;
use member_eventlib\event\TestEvent;
use member_eventconsumer\subscriber\LocalSubscriber;
use member_eventconsumer\subscriber\middleware\TestMiddleware;
use member_eventlib\tests\TestCase;

class MiddlewareTest extends TestCase
{
    public function testInvoke()
    {
        $middleware = new TestMiddleware();
        $subscriber = new LocalSubscriber('123', 'test', ['mysoft'], ['member-room' => ['*']]);
        $event = new TestEvent('mysoft', '33', '445', '5555','334dsad');

        $wrapper = new SubscriberEventWrapper($event);
        $middleware($wrapper, $subscriber);
        $this->assertInstanceOf(SubscriberEventWrapper::class, $wrapper);
    }
}
