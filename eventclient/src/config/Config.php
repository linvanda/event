<?php

namespace member_eventclient\config;

class Config
{
    const EVENT_QUEUE = 'member_event_notice';

    /**
     * 事件过滤器配置，必须是\member_eventclient\filter\IFilter的实现类
     *
     * @var array
     */
    public static $eventFilters = [
        '\member_eventclient\filter\GarbageFilter'
    ];
}
