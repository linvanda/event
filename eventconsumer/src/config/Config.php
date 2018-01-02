<?php

namespace member_eventconsumer\config;

/**
 * 订阅者配置
 *
 * Class Config
 * @package member_eventconsumer\config
 */
class Config
{
    const DISPATCH_QUEUE = 'member_event_notice';

    /**
     * 订阅者过滤器集合，必须是\member_eventconsumer\dispatcher\filter\IFilter类型
     * @var array
     */
    public static $subscriberFilters = [
        '\member_eventconsumer\dispatcher\filter\HealthFilter'
    ];
}
