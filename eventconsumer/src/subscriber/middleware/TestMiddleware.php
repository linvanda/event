<?php

namespace member_eventconsumer\subscriber\middleware;

use member_eventlib\event\IEvent;
use member_eventconsumer\subscriber\Subscriber;
use member_eventconsumer\subscriber\SubscriberEventWrapper;

class TestMiddleware implements IMiddleware
{
    /**
     * 具体的业务处理逻辑
     *
     * @param SubscriberEventWrapper $eventWrapper
     * @param Subscriber $subscriber
     * @return mixed
     */
    public function __invoke(SubscriberEventWrapper $eventWrapper, Subscriber $subscriber)
    {
        $eventWrapper->data('name', 'zhangsan')->data('age', 34);

        return $eventWrapper;
    }
}
