<?php

require_once __DIR__ . '/../vendor/autoload.php';

$content = file_get_contents('php://input');

echo json_encode(['sign' => $_GET['sign'], 'timestamp' => $_GET['timestamp'], 'nonce' => $_GET['nonce'], 'content' => $content]);
