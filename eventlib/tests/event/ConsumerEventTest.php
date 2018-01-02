<?php

namespace member_eventlib\tests\event;

use member_eventlib\event\ConsumerEvent;
use member_eventlib\event\TestEvent;
use member_eventlib\tests\TestCase;

class ConsumerEventTest extends TestCase
{
    /**
     * @var TestEvent
     */
    private $e;
    /**
     * @var ConsumerEvent
     */
    private $ce;

    public function setUp()
    {
        $this->e = new TestEvent('retesting', 'zhang san', 'male');
        $this->ce = new ConsumerEvent($this->e);

        parent::setUp();
    }

    public function testInstance()
    {
        $this->assertEquals($this->e->id(), $this->ce->id());
        $this->assertEquals($this->e->name(), $this->ce->name());
        $this->assertEquals($this->e->group(), $this->ce->group());
        $this->assertEquals($this->e->source(), $this->ce->source());
        $this->assertEquals($this->e->tenant(), $this->ce->tenant());
        $this->assertEquals($this->e->time(), $this->ce->time());
        $this->assertEquals($this->e->data(), $this->ce->data());
    }

    public function testSetProperty()
    {
        $this->expectException('\member_eventlib\exception\InvalidOperationException');

        $this->ce->firstName = 'li si';
    }
}
