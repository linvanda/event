<?php

namespace member_eventconsumer\subscriber\maintain;

use member_eventconsumer\subscriber\Subscriber;

/**
 * 订阅者健康督查接口
 *
 * Interface IHealthInspector
 * @package member_eventconsumer\subscriber\maintain
 */
interface IHealthInspector
{
    /**
     * 检查某个订阅者是否健康
     *
     * @param Subscriber $subscriber
     * @return bool
     */
    public function isHealth(Subscriber $subscriber);

    /**
     * 标记为不健康
     *
     * @return void
     */
    public function markAsUnhealthy(Subscriber $subscriber);
}
