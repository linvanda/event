<?php

namespace member_eventlib\event;

/**
 * 事件基类
 *
 * Class BaseEvent
 * @package member_eventlib\event
 */
abstract class BaseEvent implements IEvent
{
    protected $id;
    /**
     * 发生事件的租户
     * @var string
     */
    protected $tenant;

    /**
     * 事件来源
     *
     * @var string
     */
    protected $source;

    /**
     * 事件发生时间，精确到微妙
     * @var float
     */
    protected $timestamp;

    public function id()
    {
        return $this->id;
    }

    /**
     * 事件发生的租户
     *
     * @return string
     */
    public function tenant()
    {
        return $this->tenant;
    }


    public function source()
    {
        return $this->source;
    }

    public function time()
    {
        return $this->timestamp;
    }

    public function __toString()
    {
        return json_encode($this->data());
    }
}
