<?php

namespace member_eventpool;

/**
 * 房产解绑事件
 *
 * Class RoomUnboundEvent
 * @package member_eventpool
 */
class RoomUnboundEvent extends MemberBaseEvent
{
    /**
     * @data
     * @required
     * @var string
     */
    protected $roomId;

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
    protected $mid;

    /**
     * @data
     * @required
     * @var int 解绑时间，unix时间戳
     */
    protected $unbindTime;

    /**
     * @data
     * @var string 解绑原因
     */
    protected $reason;

    public function __construct(
        $tenant,
        $roomId,
        $memberId,
        $mid,
        $unbindTime = null,
        $reason = '',
        $source = self::DEFAULT_SOURCE
    ) {
        $this->setProperties(__METHOD__, func_get_args());

        if (! $this->unbindTime) {
            $this->unbindTime = time();
        }

        parent::__construct($tenant, $source);
    }

    /**
     * 分组
     * @return string
     */
    public function group()
    {
        return 'member-room';
    }

    /**
     * 分类
     * @return string
     */
    public function name()
    {
        return 'unbind';
    }
}
