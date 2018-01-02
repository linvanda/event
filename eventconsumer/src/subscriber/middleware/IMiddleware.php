<?php

namespace member_eventconsumer\subscriber\middleware;

use member_eventconsumer\subscriber\Subscriber;
use member_eventconsumer\subscriber\SubscriberEventWrapper;

/**
 * 中间件接口
 * 事件交由消费者真正处理前会经过一系列中间件处理
 *
 * 输入：SubscriberEventWrapper和Subscriber
 * 输出：SubscriberEventWrapper或SubscriberResponse。如果输出SubscriberResponse对象，
 * 订阅者中断后面的中间件以及其他流程执行，直接向外界返回接收到的SubscriberResponse对象
 *
 * 典型应用方式：
 * 当某个消费者需要自定义处理时，给该消费者定义一个中间件，在中间件中根据$event的类型执行不同的业务处理逻辑
 *（而不是针对每种事件创建一个中间件）
 * 当需要处理的事件类型较多或中间件业务很复杂时，可以给消费者创建一个入口中间件，该中间件根据事件类型等将任务分派给具体的业务处理类
 *
 * 另外，一些比较通用的中间件（可能会有多个订阅者用到）不要用订阅者命名，而是以处理的业务命名
 * 而一些只针对某个订阅者的中间件则可以以订阅者命名
 *
 * Interface IMiddleware
 * @package member_eventconsumer\subscriber\middleware
 */
interface IMiddleware
{
    /**
     * 具体的业务处理逻辑
     *
     * @param SubscriberEventWrapper $eventWrapper
     * @param Subscriber $subscriber
     * @return void|\member_eventconsumer\infrastructure\response\Response
     */
    public function __invoke(SubscriberEventWrapper $eventWrapper, Subscriber $subscriber);
}
