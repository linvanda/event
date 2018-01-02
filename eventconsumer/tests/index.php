<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * 测试http部分.可使用php -S localhost:80 启动php内置web服务器来测试
 */
$sign = new \member_eventconsumer\infrastructure\signature\Signature('33232we33232');

$content = file_get_contents('php://input');

$signObj = $sign->sign($content, $_GET['timestamp'], $_GET['nonce']);

echo json_encode(['sign' => $_GET['sign'], 'timestamp' => $_GET['timestamp'],
    'nonce' => $_GET['nonce'], 'content' => $content, 'response_sign' => $signObj->sign()]);

