#!/bin/bash

# 管理画面テスト実行スクリプト

echo "=== EvoX 管理画面テスト開始 ==="
echo ""

# データベースをテスト用にリセット
echo "1. データベースをテスト用にリセット..."
php artisan migrate:fresh --seed

# 管理画面認証テストを実行
echo ""
echo "2. 管理画面認証テストを実行..."
php artisan test tests/Feature/AdminAuthenticationTest.php

# 管理画面基本テストを実行
echo ""
echo "3. 管理画面基本テストを実行..."
php artisan test tests/Feature/AdminBasicTest.php

# 管理画面機能テストを実行（オプション）
echo ""
echo "4. 管理画面機能テストを実行（オプション）..."
php artisan test tests/Feature/AdminManagementTest.php || echo "機能テストは一部失敗する可能性があります"

# 全テストを実行
echo ""
echo "5. 全テストを実行..."
php artisan test

echo ""
echo "=== テスト完了 ==="
