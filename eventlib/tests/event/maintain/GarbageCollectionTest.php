<?php

namespace member_eventlib\tests\event\maintain;

use member_eventlib\event\maintain\GarbageCollection;
use member_eventlib\event\TestEvent;
use member_eventlib\tests\TestCase;

class GarbageCollectionTest extends TestCase
{
    public function testGarbage()
    {
        $g = new GarbageCollection;
        $e = new TestEvent('retesting', 'zhang san', 'male', '784587887877');

        //标记为垃圾事件
        for ($i = 0; $i < GarbageCollection::MAX_UNSUBSCRIBED_COUNT - 1; $i++) {
            $g->mark($e->tenant(), $e->group(), $e->name(), $e->source(), GarbageCollection::GARBAGE_UNSUBSCRIBED);
        }

        $this->assertFalse($g->isGarbage($e->tenant(), $e->group(), $e->name(), $e->source()));

        $g->mark($e->tenant(), $e->group(), $e->name(), $e->source(), GarbageCollection::GARBAGE_UNSUBSCRIBED);

        $this->assertTrue($g->isGarbage($e->tenant(), $e->group(), $e->name(), $e->source()));
    }
}
