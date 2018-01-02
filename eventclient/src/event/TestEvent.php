<?php

namespace member_eventclient\event;

class TestEvent extends DomainEvent
{
    const SOURCE_PREFIX = '1234';
    /**
     * @data
     * @required
     * @var string
     */
    protected $firstName;
    /**
     * @data
     * @var string
     */
    protected $sex;

    public function __construct($tenant, $firstName, $sex, $source = self::DEFAULT_SOURCE)
    {
        $this->setProperties(__METHOD__, func_get_args());

        parent::__construct($tenant, $source);
    }

    /**
     * 分组，如member-room
     *
     * @return string
     */
    public function group()
    {
        return 'test';
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
