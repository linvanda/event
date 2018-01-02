<?php

namespace member_eventlib\event;

use member_eventlib\exception\InvalidOperationException;

/**
 * 消费者端的统一事件模型
 * 该事件模型是为了解决事件序列化问题。
 *
 * 该事件模型是只读的
 *
 * Class ConsumerEvent
 * @package member_eventlib\event
 */
final class ConsumerEvent extends BaseEvent
{
    private $group;
    private $name;
    private $data;

    /**
     * 创建消费者事件模型
     *
     * ConsumerEvent constructor.
     * @param IEvent $event
     */
    public function __construct(IEvent $event)
    {
        $this->id = $event->id();
        $this->tenant = $event->tenant();
        $this->group = $event->group();
        $this->name = $event->name();
        $this->timestamp = $event->time();
        $this->source = $event->source();
        $this->data = $event->data();

        unset($event);
    }

    public function group()
    {
        return $this->group;
    }

    public function name()
    {
        return $this->name;
    }

    public function data()
    {
        return $this->data;
    }

    /**
     * 该模型不会执行发送操作
     *
     * @return void
     */
    public function send()
    {

    }

    /**
     * 禁止设置属性
     *
     * @param $name
     * @param $value
     * @throws InvalidOperationException
     */
    public function __set($name, $value)
    {
        throw new InvalidOperationException("can not modify ConsumerEvent's property");
    }
}
