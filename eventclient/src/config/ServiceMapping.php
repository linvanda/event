<?php

return [
    //正式程序用(此处是默认配置，外界可以调用register方法覆盖此处的配置)
    'normal' => [
        'event_publisher' => '\member_eventclient\publisher\EventPublisher',
        'source_repository' => '\member_eventclient\repository\YiiEventSourceRepository'
    ],
    //测试专用，继承normal的配置
    'unittest' => [
        'source_repository' => '\member_eventclient\repository\TestEventSourceRepository',
    ],
];
