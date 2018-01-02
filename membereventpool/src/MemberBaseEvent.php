<?php

namespace member_eventpool;

use member_eventclient\event\DomainEvent;

/**
 * 会员事件基类，用于设置会员事件公共的东西，如source，source_prefix等
 *
 * Class MemberBaseEvent
 * @package member_eventpool
 */
abstract class MemberBaseEvent extends DomainEvent
{
    const DEFAULT_SOURCE = '00000001-0000-0000-0000-000000000000';
    const SOURCE_PREFIX  = '00000001';

    public function __construct($tenant, $source = self::DEFAULT_SOURCE)
    {
        parent::__construct($tenant, $source);
    }
}
