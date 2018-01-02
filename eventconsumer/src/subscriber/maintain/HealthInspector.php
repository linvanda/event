<?php

namespace member_eventconsumer\subscriber\maintain;

use member_eventconsumer\subscriber\Subscriber;
use member_eventlib\ServiceProvider;

/**
 * 订阅者健康督察
 * 基于缓存
 *
 * Class HealthInspector
 * @package member_eventconsumer\subscriber\maintain
 */
class HealthInspector implements IHealthInspector
{
    const MAX_UNHEALTH_COUNT = 1000;

    /**
     * 检查某个订阅者是否健康
     *
     * @param Subscriber $subscriber
     * @return bool
     */
    public function isHealth(Subscriber $subscriber)
    {
        if (
            ! ($unhealthNum = ServiceProvider::cache()->get($this->key($subscriber->id())))
            || $unhealthNum < self::MAX_UNHEALTH_COUNT
        ) {
            return true;
        }

        return false;
    }

    /**
     * 标记为不健康
     *
     * @return void
     */
    public function markAsUnhealthy(Subscriber $subscriber)
    {
        $cache = ServiceProvider::cache();

        if (! ($unhealthNum = $cache->get($this->key($subscriber->id())))) {
            $unhealthNum = 0;
        }

        $unhealthNum++;

        $cache->set($this->key($subscriber->id()), $unhealthNum, 86400 * 31);
    }

    private function key($subscriberId)
    {
        return 'event_unhealth_subs_' . date('m') . $subscriberId;
    }
}
