<?php

namespace member_eventlib\event;

use member_eventlib\IPackage;

/**
 * 事件接口
 * 实现该接口的类实例都是只读的
 *
 * Interface IEvent
 * @package member_eventlib\event
 */
interface IEvent extends IPackage
{
    /**
     * 事件发生的租户
     *
     * @return string
     */
    public function tenant();

    /**
     * 分组，如member-room
     *
     * @return string
     */
    public function group();

    /**
     * 事件名称，如bind。和事件类命名不同，此处不使用过去式
     *
     * @return string
     */
    public function name();

    /**
     * 事件来源标识
     *
     * @return string
     */
    public function source();

    /**
     * 事件发生时间，精确到微妙
     * @return float
     */
    public function time();

    /**
     * 获取事件数据
     *
     * @return array
     */
    public function data();
}
