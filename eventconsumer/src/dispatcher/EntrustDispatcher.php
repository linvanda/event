<?php

namespace member_eventconsumer\dispatcher;

use member_eventconsumer\config\Config;
use member_eventconsumer\dispatcher\filter\IFilter;
use member_eventconsumer\infrastructure\response\DispatchResponse;
use member_eventlib\event\IEvent;
use member_eventconsumer\ServiceProvider;
use member_eventconsumer\subscriber\Subscriber;

/**
 * 委托分发器：为每个订阅者进行一次再分发
 *
 * Class EntrustDispatcher
 * @package member_eventconsumer\dispatcher
 */
class EntrustDispatcher implements IDispatcher
{
    protected $filters = [];

    public function __construct()
    {
        $this->init();
    }

    /**
     * 事件分发
     * 根据消费者列表分发事件
     *
     * @param IEvent $event
     * @param DispatchResponse $dispatchResponse
     * @throws \member_eventlib\exception\PackageException
     */
    public function dispatch(IEvent $event, DispatchResponse &$dispatchResponse)
    {
        //根据事件类型获取相关订阅者列表
        $subscribers = ServiceProvider::subscriberRepository()->all(
            $event->tenant(),
            $event->group(),
            $event->name(),
            $event->source()
        );

        $total = 0;
        $dispatchedNum = 0;
        foreach ($subscribers as $subscriber) {
            if (! $subscriber instanceof Subscriber) {
                continue;
            }

            //过滤
            if (! $this->filter($subscriber)) {
                continue;
            }

            $total++;

            //分发
            (new DispatchPackage($event, $subscriber->id()))->send();

            $dispatchedNum++;
        }

        //响应
        $dispatchResponse->code(200);
        $dispatchResponse->body('ok');
        $dispatchResponse->total($total);
        $dispatchResponse->dispatchedNum($dispatchedNum);
    }

    protected function init()
    {
        //初始化过滤器
        if (is_array(Config::$subscriberFilters)) {
            foreach (Config::$subscriberFilters as $filterName) {
                if (is_string($filterName) && is_subclass_of($filterName, IFilter::class)) {
                    $this->filters[] = new $filterName;
                }
            }
        }
    }

    private function filter(Subscriber $subscriber)
    {
        foreach ($this->filters as $filter) {
            if ($filter instanceof IFilter && ! $filter->filter($subscriber)) {
                ServiceProvider::logger()
                    ->warning('filter error.subscriber:' . $subscriber->id() . '.filter:' . get_class($filter));
                return false;
            }
        }

        return true;
    }
}
