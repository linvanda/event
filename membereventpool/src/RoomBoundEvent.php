<?php

namespace member_eventpool;

/**
 * 房产绑定事件
 *
 * Class RoomBoundEvent
 * @package member_eventpool
 */
class RoomBoundEvent extends MemberBaseEvent
{
    /**
     * @data
     * @required
     * @var string 房间id。必填
     */
    protected $roomId;

    /**
     * @data
     * @required
     * @var string 会员id。必填
     */
    protected $memberId;

    /**
     * @data
     * @required
     * @var string 会员mid。必填
     */
    protected $mid;

    /**
     * @data
     * @required
     * @var string 房产关系：一手业主、二手业主、同住人。必填
     */
    protected $relation;

    /**
     * @data
     * @var string 同住人与业主的关系：租客、亲属、好友等。选填
     */
    protected $relationWithOwner;

    /**
     * @data
     * @var string 关系开始时间（入住、租期开始等时间）。选填
     */
    protected $startDate;

    /**
     * @data
     * @var string 关系结束时间
     */
    protected $endDate;

    public function __construct(
        $tenant,
        $roomId,
        $memberId,
        $mid,
        $relation,
        $relationWithOwner = null,
        $startDate = null,
        $endDate = null,
        $source = self::DEFAULT_SOURCE
    ) {
        $this->setProperties(__METHOD__, func_get_args());

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
        return 'bind';
    }
}
