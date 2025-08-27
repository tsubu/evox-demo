<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Services\PenlightWebSocketService;

// WebSocketサーバーを作成
$webSocketService = new PenlightWebSocketService();

// サーバーを設定
$server = IoServer::factory(
    new HttpServer(
        new WsServer($webSocketService)
    ),
    8080
);

echo "WebSocketサーバーがポート8080で起動しました...\n";
echo "停止するには Ctrl+C を押してください\n";

// サーバーを開始
$server->run();
