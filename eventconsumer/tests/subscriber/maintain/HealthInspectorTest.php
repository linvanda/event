<?php

namespace member_eventconsumer\tests\subscriber\maintain;

use member_eventconsumer\config\Config;
use member_eventconsumer\ServiceProvider;
use member_eventconsumer\subscriber\maintain\HealthInspector;
use member_eventlib\tests\TestCase;

class HealthInspectorTest extends TestCase
{
    public function testHealth()
    {
        $h = new HealthInspector();
        $subs = ServiceProvider::subscriberRepository()->find('123');

        for ($i = 0; $i < HealthInspector::MAX_UNHEALTH_COUNT - 1; $i++) {
            $h->markAsUnhealthy($subs);
        }

        $this->assertTrue($h->isHealth($subs));

        $h->markAsUnhealthy($subs);

        $this->assertFalse($h->isHealth($subs));
    }
}
