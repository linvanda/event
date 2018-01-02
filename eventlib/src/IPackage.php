<?php

namespace member_eventlib;

/**
 * 封包接口，数据传输（如通过消息队列）的基本单元
 * Interface IPackage
 * @package my\member\event
 */
interface IPackage
{
    /**
     * 封包唯一标示
     * @return string
     */
    public function id();

    /**
     * 发送封包
     * @return void
     */
    public function send();
}
