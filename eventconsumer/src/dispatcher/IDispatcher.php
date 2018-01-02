<?php

namespace member_eventconsumer\dispatcher;
use member_eventconsumer\infrastructure\response\DispatchResponse;
use member_eventlib\event\IEvent;

/**
 * 事件分发器接口
 * 分发器负责将消息分发给相关订阅者
 *
 * Interface IDispatcher
 * @package member_eventconsumer\dispatcher
 */
interface IDispatcher
{
    /**
     * 事件分发
     * 根据消费者列表分发事件
     *
     * @param IEvent $event
     * @param DispatchResponse $dispatchResponse
     * @return void
     */
    public function dispatch(IEvent $event, DispatchResponse &$dispatchResponse);
}
