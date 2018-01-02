<?php

namespace member_eventclient\filter;

use member_eventclient\event\DomainEvent;

/**
 * 领域事件过滤器接口
 * 领域事件发布之前会执行一系列的过滤
 *
 * Interface IFilter
 * @package member_eventclient\filter
 */
interface IFilter
{
    /**
     * 对DomainEvent对象执行过滤
     *
     * @param DomainEvent $event
     * @return bool 该事件是否合法
     */
    public function filter(DomainEvent $event);
}
