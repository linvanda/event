<?php

namespace member_eventpool;

/**
 * 退出登录事件
 *
 * Class LogoutEvent
 * @package member_eventpool
 */
class LogoutEvent extends MemberBaseEvent
{
    /**
     * @data
     * @required
     * @var string
     */
    protected $mid;

    /**
     * @data
     * @required
     * @var string
     */
    protected $memberId;

    /**
     * @data
     * @required
     * @var string
     */
    protected $openid;

    /**
     * @data
     * @required
     * @var string
     */
    protected $logoutTime;

    public function __construct($tenant, $mid, $memberId, $openid, $source = self::DEFAULT_SOURCE)
    {
        $this->setProperties(__METHOD__, func_get_args());

        $this->logoutTime = time();

        parent::__construct($tenant, $source);
    }

    /**
     * 分组，如member-room
     *
     * @return string
     */
    public function group()
    {
        return 'member';
    }

    /**
     * 事件名称，如bind。和事件类命名不同，此处不使用过去式
     *
     * @return string
     */
    public function name()
    {
        return 'logout';
    }
}
