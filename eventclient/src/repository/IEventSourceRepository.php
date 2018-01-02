<?php

namespace member_eventclient\repository;

interface IEventSourceRepository
{
    /**
     * 所有事件源对象
     *
     * @return \member_eventclient\source\EventSource
     */
    public function all();
}
