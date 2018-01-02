<?php

namespace member_eventclient\publisher;

use member_eventclient\config\Config;
use member_eventlib\event\ConsumerEvent;
use member_eventclient\event\DomainEvent;
use member_eventclient\ServiceProvider;
use member_eventclient\filter\IFilter;

/**
 * 事件发布器,封装事件封包的发布策略
 *
 * Class EventPublisher
 * @package member_eventclient\publisher
 */
class EventPublisher
{
    /**
     * @var array 领域事件过滤器
     */
    protected $filters = [];

    public function __construct()
    {
        $this->init();
    }

    public function publish(DomainEvent $event)
    {
        //检查该租户是否需要发布该类型事件
        foreach ($this->filters as $filter) {
            if ($filter instanceof IFilter && ! $filter->filter($event)) {
                ServiceProvider::logger()
                    ->warning('filter error.event:' . $event->id() . '.filter:' . get_class($filter));
                return ;
            }
        }

        //事件发布器将领域事件转换成消费事件发出
        ServiceProvider::queue()->enqueue(Config::EVENT_QUEUE, new ConsumerEvent($event));
    }

    protected function init()
    {
        //初始化过滤器
        if (is_array(Config::$eventFilters)) {
            foreach (Config::$eventFilters as $filterName) {
                if (is_string($filterName) && is_subclass_of($filterName, IFilter::class)) {
                    $this->filters[] = new $filterName;
                }
            }
        }
    }
}
