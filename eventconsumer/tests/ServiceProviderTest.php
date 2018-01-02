<?php

namespace member_eventconsumer\tests;

use member_eventlib\infrastructure\TestMessageQueue;
use member_eventconsumer\ServiceProvider;
use member_eventlib\tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function testCache()
    {
        $cache = ServiceProvider::cache();

        $this->assertInstanceOf(\member_eventlib\infrastructure\ICache::class, $cache);

        $cache->set('test', 'my test');
        $this->assertEquals($cache->get('test') == 'my test', true);

        $this->assertTrue($cache->exists('test'));

        $cache->delete('test');

        $this->assertFalse($cache->exists('test'));
    }

    public function testQueue()
    {
        $queue = ServiceProvider::queue();

        $this->assertInstanceOf(\member_eventlib\infrastructure\IMessageQueue::class, $queue);

        $queue->enqueue('test_queue', ['name', 'sex']);

        //只有测试队列才检查提供dequeue检测
        if ($queue instanceof TestMessageQueue) {
            $this->assertEquals($queue->dequeue('test_queue') == ['name', 'sex'], true);
        }
    }

    public function testRegister()
    {
        ServiceProvider::register('cache', \member_eventlib\infrastructure\YiiCache::class);

        $cache = ServiceProvider::get('cache');
        $cache2 = ServiceProvider::cache();

        $this->assertInstanceOf(\member_eventlib\infrastructure\YiiCache::class, $cache);
        $this->assertInstanceOf(\member_eventlib\infrastructure\YiiCache::class, $cache2);
        $this->assertTrue($cache == $cache2);

        //改回来
        ServiceProvider::register('cache', \member_eventlib\infrastructure\TestCache::class);
    }
}
