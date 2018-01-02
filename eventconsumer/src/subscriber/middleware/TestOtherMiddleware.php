<?php

namespace member_eventconsumer\subscriber\middleware;

use member_eventconsumer\infrastructure\response\SubscriberResponse;
use member_eventconsumer\subscriber\Subscriber;
use member_eventconsumer\subscriber\SubscriberEventWrapper;

class TestOtherMiddleware implements IMiddleware
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
        $eventWrapper->data('sex', 'male')->data('love', 'nv');

        return new SubscriberResponse(300, 'some error');
    }
}
