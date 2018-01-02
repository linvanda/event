<?php

namespace member_eventlib\event;

class TestEvent extends BaseEvent
{

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

    /**
     * 获取事件数据
     *
     * @return array
     */
    public function data()
    {
        return [
            'type' => 'event',
            'id' => '32234323232423',
            'group' => $this->group(),
            'name' => $this->name(),
            'tenant' => 'test',
            'timestamp' => '2332324323232',
            'source' => '2342332423234324',
            'body' => [
                'lover' => 'sanfun'
            ]
        ];
    }

    /**
     * 发送封包
     * @return void
     */
    public function send()
    {

    }

    /**
     * 事件来源标识
     *
     * @return string
     */
    public function source()
    {
        return '1234';
    }
}
