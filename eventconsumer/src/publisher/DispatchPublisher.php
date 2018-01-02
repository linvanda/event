<?php

namespace member_eventconsumer\publisher;

use member_eventconsumer\config\Config;
use member_eventconsumer\dispatcher\DispatchPackage;
use member_eventconsumer\ServiceProvider;

/**
 * 分发封包发布器，封装分发封包的发布策略
 *
 * Class DispatchPublisher
 * @package member_eventconsumer\publisher
 */
class DispatchPublisher
{
    public function publish(DispatchPackage $package)
    {
        ServiceProvider::queue()->enqueue(Config::DISPATCH_QUEUE, $package);
    }
}
