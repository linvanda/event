<?php

namespace member_eventconsumer\infrastructure\signature;

use member_eventlib\exception\InvalidOperationException;

/**
 * 基础签名器
 *
 * Class Signature
 * @package member_eventconsumer\infrastructure\signature
 */
class Signature implements ISignature
{
    protected $secret;

    public function __construct($secret = '')
    {
        if ($secret) {
            $this->secret($secret);
        }
    }

    /**
     * strtolower(md5(时间戳字符串+$str+secret+随机字符串))
     *
     * @param string $str
     * @param null $timestamp
     * @param null $nonce
     * @return SignDTO
     * @throws InvalidOperationException
     */
    public function sign($str, $timestamp = null, $nonce = null)
    {
        if (! $this->secret) {
            throw new InvalidOperationException("must set secret before sign");
        }

        $nonce = $nonce ?: $this->nonce();
        $timestamp = $timestamp ?: time();

        return new SignDTO(
            strtolower(md5($timestamp . $str . $this->secret . $nonce)),
            $timestamp,
            $nonce
        );
    }

    /**
     * 设置秘钥
     *
     * @param string $secret
     * @return void
     */
    public function secret($secret)
    {
        if (! is_string($secret) || ! $secret) {
            throw new \InvalidArgumentException("非法的secret:$secret");
        }

        $this->secret = $secret;
    }

    public function nonce() {
        return mt_rand(1000, 10000);
    }
}
