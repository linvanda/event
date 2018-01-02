<?php

namespace member_eventconsumer\subscriber;

use member_eventconsumer\infrastructure\response\SubscriberResponse;

/**
 * 本地消费者
 * 有些第三方需要我们调用对方的业务接口而不是事件订阅接口（对方不订阅我们的事件），
 * 此时可将第三方创建为本地消费者，然后通过中间件实现对方业务接口调用
 *
 * Class LocalSubscriber
 * @package member_eventconsumer\subscriber
 */
class LocalSubscriber extends Subscriber
{
    /**
     * 消费逻辑
     * 本地订阅者默认没有任何自身的业务逻辑，一般通过中间件实现
     *
     * @param SubscriberEventWrapper $eventWrapperWrapper
     * @param SubscriberResponse $response
     * @return void
     */
    protected function internalConsume(SubscriberEventWrapper $eventWrapperWrapper, SubscriberResponse &$response)
    {
        //本地消费者目前没有自身的消费逻辑
        $response->code(200);
        $response->body('ok');
    }
}
