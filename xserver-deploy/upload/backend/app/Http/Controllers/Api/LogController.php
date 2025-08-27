<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * フロントエンドから送信されたログを保存
     */
    public function store(Request $request)
    {
        try {
            $logs = $request->input('logs', []);
            
            if (empty($logs)) {
                return response()->json(['message' => 'No logs provided'], 400);
            }

            // ログファイルのパスを生成
            $date = now()->format('Y-m-d');
            $logFile = "frontend-logs/frontend-{$date}.log";
            
            // ログディレクトリが存在しない場合は作成
            $logDir = storage_path('logs/frontend-logs');
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }

            // 各ログエントリをファイルに書き込み
            foreach ($logs as $logEntry) {
                $logLine = sprintf(
                    "[%s] [%s] %s - %s - %s\n",
                    $logEntry['timestamp'] ?? now()->toISOString(),
                    $logEntry['level'] ?? 'INFO',
                    $logEntry['message'] ?? '',
                    $logEntry['url'] ?? '',
                    json_encode($logEntry['data'] ?? [])
                );
                
                // ログファイルに追記
                file_put_contents(
                    storage_path("logs/{$logFile}"),
                    $logLine,
                    FILE_APPEND | LOCK_EX
                );
            }

            // Laravelのログにも記録
            Log::info('Frontend logs stored', [
                'count' => count($logs),
                'file' => $logFile
            ]);

            return response()->json([
                'message' => 'Logs stored successfully',
                'count' => count($logs),
                'file' => $logFile
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to store frontend logs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to store logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ログファイルの一覧を取得
     */
    public function index()
    {
        try {
            $logDir = storage_path('logs/frontend-logs');
            
            if (!file_exists($logDir)) {
                return response()->json(['files' => []]);
            }

            $files = glob($logDir . '/*.log');
            $logFiles = [];

            foreach ($files as $file) {
                $logFiles[] = [
                    'name' => basename($file),
                    'size' => filesize($file),
                    'modified' => date('Y-m-d H:i:s', filemtime($file))
                ];
            }

            // 日付順にソート（新しい順）
            usort($logFiles, function($a, $b) {
                return strcmp($b['modified'], $a['modified']);
            });

            return response()->json(['files' => $logFiles]);

        } catch (\Exception $e) {
            Log::error('Failed to get log files', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to get log files',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 特定のログファイルの内容を取得
     */
    public function show($filename)
    {
        try {
            $logFile = storage_path("logs/frontend-logs/{$filename}");
            
            if (!file_exists($logFile)) {
                return response()->json(['message' => 'Log file not found'], 404);
            }

            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);
            
            // 最後の100行のみを返す
            $lines = array_filter($lines);
            $lines = array_slice($lines, -100);

            return response()->json([
                'filename' => $filename,
                'lines' => $lines,
                'total_lines' => count(explode("\n", $content)) - 1
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to read log file', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to read log file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
