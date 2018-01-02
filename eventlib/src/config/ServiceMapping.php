<?php

return [
    //正式程序用(此处是默认配置，外界可以调用register方法覆盖此处的配置)
    'normal' => [
        'cache' => '\member_eventlib\infrastructure\YiiCache',
        'queue' => '\member_eventlib\infrastructure\YiiMessageQueue',
        'log' => '\member_eventlib\infrastructure\log\YiiLog',
        'garbage_collection' => '\member_eventlib\event\maintain\GarbageCollection',
    ],
    //测试专用，继承normal的配置
    'unittest' => [
        'cache' => '\member_eventlib\infrastructure\TestCache',
        'queue' => '\member_eventlib\infrastructure\TestMessageQueue',
        'log' => '\member_eventlib\infrastructure\log\TestLog',
    ],
];
