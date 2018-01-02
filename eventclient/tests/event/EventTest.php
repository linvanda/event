<?php

namespace member_eventclient\tests\event;

use member_eventclient\event\TestEvent;
use member_eventlib\tests\TestCase;

class EventTest extends TestCase
{
    /**
     * 基本验证
     */
    public function testEvent()
    {
        $e = new TestEvent('retesting', 'zhang san', '男', '123456789');
        $e->send();

        $this->assertEquals($e->name(), 'test');
        $this->assertEquals($e->data()['body']['first_name'], 'zhang san');
        $this->assertEquals($e->data()['body']['sex'], '男');
    }

    /**
     * 属性设置
     *
     * @throws \member_eventlib\exception\EventValidateException
     */
    public function testSetProperty()
    {
        $e = new TestEvent('retesting', 'zhang san', '男', '123456789');
        $e->firstName = 'li si';
        $this->assertEquals($e->data()['body']['first_name'], 'li si');

        $e->send();

        //send后事件对象不可再设置属性值
        $this->expectException('member_eventlib\exception\InvalidOperationException');

        $e->firstName = 'wang wu';
    }

    /**
     * 验证器
     *
     * @throws \member_eventlib\exception\EventValidateException
     */
    public function testValidate()
    {
        $e = new TestEvent('retesting', null, '男', '12345678');

        $this->expectException('member_eventlib\exception\EventValidateException');
        $e->send();

        $e2 = new TestEvent('retesting', 'zhangsan', '男', '456789');
        $this->expectException('member_eventlib\exception\EventValidateException');
        $e2->send();
    }
}
