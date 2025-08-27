#!/bin/bash
echo "=== Xserver 権限設定開始 ==="

# ストレージ権限
chmod -R 755 backend/storage/
chmod -R 755 backend/bootstrap/cache/
chmod -R 755 backend/storage/logs/

# ログファイル作成
touch backend/storage/logs/laravel.log
chmod 666 backend/storage/logs/laravel.log

echo "✅ 権限設定完了"
