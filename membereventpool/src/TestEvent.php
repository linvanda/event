<?php

namespace member_eventpool;

/**
 * 退出登录事件
 *
 * Class LogoutEvent
 * @package member_eventpool
 */
class TestEvent extends MemberBaseEvent
{
    /**
     * @data
     * @var string
     */
    protected $memberName;

    public function __construct($tenant, $memberName, $source = self::DEFAULT_SOURCE)
    {
        $this->memberName = $memberName;

        parent::__construct($tenant, $source);
    }

    /**
     * 分组，如member-room
     *
     * @return string
     */
    public function group()
    {
        return 'member-test';
    }

    /**
     * 事件名称，如bind。和事件类命名不同，此处不使用过去式
     *
     * @return string
     */
    public function name()
    {
        return 'test';
    }
}
