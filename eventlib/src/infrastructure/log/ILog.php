<?php

namespace member_eventlib\infrastructure\log;

interface ILog
{
    public function error($msg);

    public function warning($msg);
}
