<?php

namespace member_eventconsumer\tests\subscriber;

use member_eventconsumer\infrastructure\response\SubscriberResponse;
use member_eventlib\event\TestEvent;
use member_eventconsumer\subscriber\LocalSubscriber;
use member_eventconsumer\subscriber\SubscriberEventWrapper;
use member_eventlib\tests\TestCase;

class SubscriberTest extends TestCase
{
    /**
     * 本地消费
     */
    public function testLocalSubscriberConsume()
    {
        $events = [
            'retesting' => [
                'member-room' => [
                    'event' => ['*']
                ]
            ]
        ];

        $subscriber = new LocalSubscriber('123', 'test', $events);
        $event =  new TestEvent('mysoft', '33', '445', '5555','334dsad');
        $response = new SubscriberResponse();

        $subscriber->consume(new SubscriberEventWrapper($event), $response);

        $this->assertTrue($response->code() == 200);
    }
}
