<?php

namespace member_eventconsumer\infrastructure\signature;

use member_eventlib\infrastructure\utils\ObjectUtilTrait;

final class SignDTO
{
    use ObjectUtilTrait;

    /**
     * @var string 签名结果
     */
    private $sign;
    /**
     * @var int 时间戳
     */
    private $timestamp;
    /**
     * @var string 随机字符串
     */
    private $nonce;

    public function __construct($sign, $timestamp, $nonce)
    {
        $this->setProperties(__METHOD__, func_get_args());
    }

    public function sign()
    {
        return $this->sign;
    }

    public function timestamp()
    {
        return $this->timestamp;
    }

    public function nonce()
    {
        return $this->nonce;
    }
}
