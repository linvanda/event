<?php
require_once __DIR__ . '/../vendor/autoload.php';

$sign = new \member_eventconsumer\infrastructure\signature\Signature('33232we33232');
$c = new \member_eventconsumer\infrastructure\httpclient\HttpsClient($sign);

$c->certInfo([
    'cert' => 'whwg-cert.pem',
    'cert_ca' => 'whwg-ca-cert.cer',
    'cert_pwd' => 'weigaojf&123'
]);

$p = [
    'name' => 'zhagnsan',
    'sex' => 'male'
];
$result = $c->post('https://www.upcard.com.cn:7071/mcws/rest/cardholder/msgNotify', $p);
var_export($result);

