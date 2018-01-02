<?php

namespace member_eventconsumer\dispatcher\filter;

use member_eventconsumer\ServiceProvider;
use member_eventconsumer\subscriber\Subscriber;

/**
 * 健康过滤器
 * 过滤长期不予响应或响应错误的非健康订阅者
 *
 * Class HealthFilter
 * @package member_eventconsumer\dispatcher\filter
 */
class HealthFilter implements IFilter
{

    /**
     * 过滤
     *
     * @param Subscriber $subscriber
     * @return bool 返回false表示该订阅者有问题，不予分发
     */
    public function filter(Subscriber $subscriber)
    {
        return ServiceProvider::healthInspector()->isHealth($subscriber);
    }
}
