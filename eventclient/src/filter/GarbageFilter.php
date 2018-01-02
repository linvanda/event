<?php

namespace member_eventclient\filter;

use member_eventclient\event\DomainEvent;
use member_eventclient\ServiceProvider;

/**
 * 垃圾事件过滤器
 * 如长期没有任何订阅者的事件
 *
 * Class GarbageFilter
 * @package member_eventclient\filter
 */
class GarbageFilter implements IFilter
{
    public function filter(DomainEvent $event)
    {
        return ! ServiceProvider::garbageCollection()->isGarbage(
            $event->tenant(),
            $event->group(),
            $event->name(),
            $event->source()
        );
    }
}
