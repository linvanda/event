<?php

namespace member_eventconsumer\subscriber;

use member_eventconsumer\dispatcher\DispatchPackage;
use member_eventconsumer\infrastructure\response\SubscriberResponse;
use member_eventlib\exception\PackageException;
use member_eventconsumer\ServiceProvider;

/**
 * 订阅者代理
 * 收取分发封包（DispatchPackage）并解析，根据封包内容创建相应订阅者并调用该订阅者的consume方法消费事件信息
 *
 * Class SubscriberProxy
 * @package member_eventconsumer\subscriber
 */
class SubscriberProxy
{
    public function act(DispatchPackage $package, SubscriberResponse &$response)
    {
        $subscriber = ServiceProvider::subscriberRepository()->find($package->subscriberFlag());

        if (!$subscriber) {
            throw new PackageException("dispatchPackage consume error:no subscriber");
        }

        $subscriber->consume(new SubscriberEventWrapper($package->event()), $response);
    }
}
