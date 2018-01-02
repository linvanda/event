<?php

namespace member_eventconsumer\dispatcher\filter;

use member_eventconsumer\subscriber\Subscriber;

/**
 * 订阅者过滤器接口，供分发器用
 *
 * Interface IFilter
 * @package member_eventconsumer\dispatcher\filter
 */
interface IFilter
{
    /**
     * 过滤
     *
     * @param Subscriber $subscriber
     * @return bool 返回false表示该订阅者有问题，不予分发
     */
    public function filter(Subscriber $subscriber);
}
