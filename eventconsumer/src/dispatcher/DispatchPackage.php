<?php

namespace member_eventconsumer\dispatcher;

use member_eventconsumer\config\Config;
use member_eventlib\event\IEvent;
use member_eventlib\exception\PackageException;
use member_eventlib\infrastructure\IdGenerator;
use member_eventlib\IPackage;
use member_eventconsumer\ServiceProvider;

/**
 * 分发封包，内部组装了事件和订阅者
 * Class DispatchPackage
 * @package member_eventconsumer\dispatcher
 */
class DispatchPackage implements IPackage
{
    const MAX_TTL = 3;
    const TICK_INTEVEL = 5;

    protected $id;
    protected $ttl;
    protected $event;
    protected $subscriberId;
    protected $nextWakeUpTime;

    /**
     * DispatchPackage constructor.
     * @param IEvent $event 事件
     * @param string $subscriberId 订阅者id
     */
    public function __construct(IEvent $event, $subscriberId)
    {
        $this->id = IdGenerator::id();
        $this->event = $event;
        $this->subscriberId = $subscriberId;
        $this->ttl = self::MAX_TTL;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return IEvent
     */
    public function event()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function subscriberFlag()
    {
        return $this->subscriberId;
    }

    /**
     * 封包发送
     * 每发送一次，需要减少一次ttl值，当ttl=0时停止发送
     *
     * @return void
     */
    public function send()
    {
        if ($this->ttl < 1) {
            throw new PackageException("package fail in send for " . self::MAX_TTL . " times.stop!!!");
        }

        $this->tick();

        ServiceProvider::dispatchPublisher()->publish($this);
    }

    /**
     * 当封包处于休眠态，等待该封包苏醒
     */
    public function sink()
    {
        sleep(1);
        ServiceProvider::dispatchPublisher()->publish($this);
    }

    /**
     * 封包是否处于苏醒状态，外界不会处理处于休眠态的封包
     *
     * @return bool
     */
    public function isAwake()
    {
        return ! $this->nextWakeUpTime || time() >= $this->nextWakeUpTime;
    }

    /**
     * 减少一次生命值，并根据发送次数设置下次激活时间
     * 当ttl=0时表示该封包重发超过次数仍未发送成功，停止重发
     *
     * @return void
     */
    protected function tick()
    {
        $this->nextWakeUpTime = time() + (self::MAX_TTL - $this->ttl) * self::TICK_INTEVEL;
        $this->ttl--;
    }
}
