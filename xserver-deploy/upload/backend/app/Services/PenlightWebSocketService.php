<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PenlightWebSocketService
{
    protected $clients;
    protected $rooms;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
    }

    // 一時的にRatchetメソッドを無効化
    public function onOpen($conn)
    {
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    public function onMessage($from, $msg)
    {
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    public function onClose($conn)
    {
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    public function onError($conn, \Exception $e)
    {
        Log::error("WebSocket機能は一時的に無効化されています");
    }

    protected function joinRoom($client, $roomId)
    {
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    protected function leaveRoom($client, $roomId)
    {
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    protected function removeFromAllRooms($client)
    {
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    protected function sendToClient($client, $data)
    {
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    protected function handlePenlightControl($from, $data)
    {
        // ペンライト制御メッセージの処理
        Log::info("WebSocket機能は一時的に無効化されています");
    }

    // 外部から呼び出されるメソッド
    public function broadcastToRoom($roomId, $data)
    {
        if (!isset($this->rooms[$roomId])) {
            Log::info("ルーム {$roomId} に接続中のクライアントがありません");
            return;
        }

        $message = json_encode($data);
        
        foreach ($this->rooms[$roomId] as $client) {
            $this->sendToClient($client, $data);
        }
        
        Log::info("ルーム {$roomId} にメッセージをブロードキャスト: " . $message);
    }

    public function broadcastPenlightPreset($roomId, $presetData)
    {
        $data = [
            'type' => 'penlight_preset',
            'preset' => $presetData,
            'timestamp' => now()->timestamp
        ];
        
        $this->broadcastToRoom($roomId, $data);
    }

    public function getRoomStats()
    {
        $stats = [];
        
        foreach ($this->rooms as $roomId => $clients) {
            $stats[$roomId] = [
                'room_id' => $roomId,
                'client_count' => $clients->count(),
                'clients' => []
            ];
            
            foreach ($clients as $client) {
                $stats[$roomId]['clients'][] = $client->resourceId;
            }
        }
        
        return $stats;
    }
}
