<?php

namespace member_eventlib\event\maintain;

/**
 * 事件垃圾收集器接口
 *
 * Interface IGarbageCollection
 * @package member_eventlib\event\maintain
 */
interface IGarbageCollection
{
    //无任何订阅者
    const GARBAGE_UNSUBSCRIBED = 1;

    /**
     * 将某租户的某类型事件标记为垃圾事件
     *
     * @param string $tenant
     * @param string $group
     * @param string $name
     * @param string $source
     * @param int $garbageType
     * @return void
     */
    public function mark($tenant, $group, $name, $source, $garbageType);

    /**
     * 判断某租户某类型事件是否为垃圾事件
     *
     * @param string $tenant
     * @param string $group
     * @param string $name
     * @param int $source
     * @return bool
     */
    public function isGarbage($tenant, $group, $name, $source);
}
