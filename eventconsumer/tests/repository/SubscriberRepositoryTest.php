<?php

namespace member_eventconsumer\tests\repository;

use member_eventconsumer\repository\TestSubscriberRepository;
use member_eventconsumer\ServiceProvider;
use member_eventlib\tests\TestCase;

class SubscriberRepositoryTest extends TestCase
{
    public function testFind()
    {
        $rep = ServiceProvider::subscriberRepository();
        $sub1 = $rep->find('123');

        $this->assertInstanceOf(\member_eventconsumer\subscriber\RemoteSubscriber::class, $sub1);
        $this->assertTrue($sub1->id() == '123');
    }

    public function testAll()
    {
        $rep = new TestSubscriberRepository();

        $sub1 = $rep->all('retesting', 'member-room', 'bind', '000001-00123456455343');
        $sub11 = $rep->all('retesting', 'member-room', 'bind', '000002-00123456455343');
        $sub2 = $rep->all('mysoft', 'member-room', 'bind', '000001-00123456455343');
        $sub3 = $rep->all('mysoft', 'member-room', 'bind', '000002-00123456455343');

        $this->assertEquals(count($sub1), 1);
        $this->assertEquals(count($sub11), 0);
        $this->assertEquals(count($sub2), 0);
        $this->assertEquals(count($sub3), 1);
    }
}
