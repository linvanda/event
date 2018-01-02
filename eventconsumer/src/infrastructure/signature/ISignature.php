<?php

namespace member_eventconsumer\infrastructure\signature;

/**
 * 签名接口
 *
 * Interface ISignature
 * @package member_eventconsumer\infrastructure\signature
 */
interface ISignature
{
   /**
    * 签名
    *
    * @param string $str
    * @return SignDTO
    */
   public function sign($str);

   /**
    * 设置秘钥
    *
    * @param string $secret
    * @return void
    */
   public function secret($secret);
}
