<?php

namespace member_eventlib\infrastructure\log;

class TestLog implements ILog
{

    public function error($msg)
    {
        echo $msg . "\n";
    }

    public function warning($msg)
    {
        echo $msg . "\n";
    }
}
