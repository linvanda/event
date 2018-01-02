<?php

namespace member_eventconsumer\tests\subscriber;

use member_eventlib\event\TestEvent;
use member_eventconsumer\subscriber\SubscriberEventWrapper;
use member_eventlib\tests\TestCase;

class SubscriberEventWrapperTest extends TestCase
{
    public function testWrapper()
    {
        $event =  new TestEvent('mysoft', '33', '445', '5555','334dsad');
        $wrapper = new SubscriberEventWrapper($event);

        //下标设置和访问数据
        $wrapper['myname'] = 'zhangsan';
        $this->assertEquals($wrapper['myname'], 'zhangsan');
        //删除数据
        unset($wrapper['myname']);
        $this->assertNull($wrapper['myname']);

        //获取全数据
        $tdata = $wrapper->data();
        $this->assertEquals($tdata['type'], 'event');
    }
}
