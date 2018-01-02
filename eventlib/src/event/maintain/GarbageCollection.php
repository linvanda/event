<?php

namespace member_eventlib\event\maintain;

use member_eventlib\ServiceProvider;

/**
 * 事件垃圾收集器
 * 该收集器基于cache
 *
 * Class GarbageCollection
 * @package member_eventlib\event\maintain
 */
class GarbageCollection implements IGarbageCollection
{
    const MAX_UNSUBSCRIBED_COUNT = 1000;

    /**
     * 标记为垃圾事件类型
     *
     * @param string $tenant
     * @param string $group
     * @param string $name
     * @param string $source
     * @param int $garbageType
     */
    public function mark($tenant, $group, $name, $source, $garbageType)
    {
        switch ($garbageType) {
            case self::GARBAGE_UNSUBSCRIBED:
                $this->markAsUnsubscribed($tenant, $group, $name, $source);
                break;
        }
    }

    /**
     * 是否垃圾事件类型
     * 目前仅判断是否无任何订阅者
     *
     * @param string $tenant
     * @param string $group
     * @param string $name
     * @param string $source
     * @return bool
     */
    public function isGarbage($tenant, $group, $name, $source)
    {
        return $this->isUnsubscribedGarbage($tenant, $group, $name, $source);
    }

    private function markAsUnsubscribed($tenant, $group, $name, $source)
    {
        $cache = ServiceProvider::cache();
        $key = $this->cacheKey($tenant, $group, $source, 'unsubscribed_');

        if (! ($mapping = $cache->get($key))) {
            $mapping = [];
        }

        if (! $mapping[$name]) {
            $mapping[$name] = 0;
        }

        $mapping[$name] += 1;

        $cache->set($key, $mapping, 86400 * 31);
    }

    /**
     * 是否为无任何订阅者订阅的垃圾事件
     *
     * @param $tenant
     * @param $group
     * @param $name
     * @param $source
     * @return bool
     */
    private function isUnsubscribedGarbage($tenant, $group, $name, $source)
    {
        $cache = ServiceProvider::cache();

        $mapping = $cache->get($this->cacheKey($tenant, $group, $source, 'unsubscribed_'));

        if (! $mapping || ! $mapping[$name] || $mapping[$name] < self::MAX_UNSUBSCRIBED_COUNT) {
            return false;
        }

        return true;
    }

    private function cacheKey($tenant, $group, $source, $prefix = '')
    {
        return md5($prefix . $tenant . $group . $source . date('m'));
    }
}
