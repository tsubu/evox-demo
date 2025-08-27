#!/bin/bash

# シンプルSSHデプロイスクリプト
# 使用方法: ./simple-deploy.sh

set -e

# 色付き出力用の関数
print_info() {
    echo -e "\033[1;34mℹ️  $1\033[0m"
}

print_success() {
    echo -e "\033[1;32m✅ $1\033[0m"
}

print_error() {
    echo -e "\033[1;31m❌ $1\033[0m"
}

print_header() {
    echo -e "\033[1;36m"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                EvoX シンプルデプロイ                        ║"
    echo "║              既存SSHサーバーへの転送                        ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo -e "\033[0m"
}

print_header

# 設定値の入力
print_info "=== SSH接続設定 ==="
read -p "サーバーIP/ドメイン: " SSH_HOST
read -p "ユーザー名: " SSH_USER
read -p "SSHポート (デフォルト: 22): " SSH_PORT
SSH_PORT=${SSH_PORT:-22}

read -p "リモートディレクトリ (デフォルト: /var/www/evox): " REMOTE_DIR
REMOTE_DIR=${REMOTE_DIR:-/var/www/evox}

print_info ""
print_info "=== アプリケーション設定 ==="
read -p "アプリケーションURL: " APP_URL

print_info ""
print_info "=== データベース設定 ==="
read -p "データベースホスト: " DB_HOST
read -p "データベース名: " DB_DATABASE
read -p "データベースユーザー: " DB_USERNAME
read -s -p "データベースパスワード: " DB_PASSWORD
echo ""

print_info ""
print_info "=== Twilio設定 ==="
read -p "Twilio Account SID: " TWILIO_ACCOUNT_SID
read -p "Twilio Auth Token: " TWILIO_AUTH_TOKEN
read -p "Twilio From Number: " TWILIO_FROM_NUMBER

# スクリプトディレクトリ
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROD_DIR="$(dirname "$SCRIPT_DIR")"

# 本番環境ファイルの準備確認
if [ ! -d "$PROD_DIR/backend" ] || [ ! -d "$PROD_DIR/frontend" ]; then
    print_info "本番環境ファイルを準備中..."
    
    if [ -f "$PROD_DIR/prepare-production.sh" ]; then
        chmod +x "$PROD_DIR/prepare-production.sh"
        "$PROD_DIR/prepare-production.sh"
    else
        print_error "prepare-production.shが見つかりません"
        exit 1
    fi
fi

# SSH接続テスト
print_info "SSH接続をテスト中..."
if ! ssh -p $SSH_PORT -o ConnectTimeout=10 -o BatchMode=yes $SSH_USER@$SSH_HOST "echo 'SSH接続成功'" 2>/dev/null; then
    print_error "SSH接続に失敗しました"
    print_info "SSHキーが設定されているか確認してください"
    exit 1
fi

print_success "SSH接続が確認されました"

# デプロイ確認
print_info ""
print_info "=== デプロイ情報 ==="
print_info "サーバー: $SSH_HOST:$SSH_PORT"
print_info "ユーザー: $SSH_USER"
print_info "リモートディレクトリ: $REMOTE_DIR"
print_info ""

read -p "デプロイを開始しますか？ (y/N): " confirm

if [[ ! $confirm =~ ^[Yy]$ ]]; then
    print_info "デプロイをキャンセルしました"
    exit 0
fi

# デプロイ実行
print_info ""
print_info "=== デプロイ開始 ==="

# 1. リモートディレクトリの作成
print_info "リモートディレクトリを作成中..."
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST "mkdir -p $REMOTE_DIR"

# 2. バックエンドファイルの転送
print_info "バックエンドファイルを転送中..."
rsync -avz -e "ssh -p $SSH_PORT" \
    --exclude='vendor/' \
    --exclude='node_modules/' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='.env' \
    --exclude='.git/' \
    --exclude='*.log' \
    --exclude='test_*.php' \
    --exclude='*_test.php' \
    --exclude='*.sh' \
    "$PROD_DIR/backend/" "$SSH_USER@$SSH_HOST:$REMOTE_DIR/backend/"

# 3. フロントエンドファイルの転送
print_info "フロントエンドファイルを転送中..."
rsync -avz -e "ssh -p $SSH_PORT" \
    --exclude='node_modules/' \
    --exclude='dist/' \
    --exclude='.env' \
    --exclude='.git/' \
    --exclude='*.log' \
    "$PROD_DIR/frontend/" "$SSH_USER@$SSH_HOST:$REMOTE_DIR/frontend/"

# 4. 本番環境用.envファイルの作成と転送
print_info "環境設定ファイルを作成中..."

# バックエンド用.env
cat > /tmp/backend.env << EOF
APP_NAME=EvoX
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=$APP_URL

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=$DB_HOST
DB_PORT=5432
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=$TWILIO_ACCOUNT_SID
TWILIO_AUTH_TOKEN=$TWILIO_AUTH_TOKEN
TWILIO_FROM_NUMBER=$TWILIO_FROM_NUMBER
EOF

# フロントエンド用.env
cat > /tmp/frontend.env << EOF
VITE_APP_NAME=EvoX
VITE_API_BASE_URL=$APP_URL/api
VITE_APP_URL=$APP_URL
EOF

# 環境ファイルを転送
scp -P $SSH_PORT /tmp/backend.env $SSH_USER@$SSH_HOST:$REMOTE_DIR/backend/.env
scp -P $SSH_PORT /tmp/frontend.env $SSH_USER@$SSH_HOST:$REMOTE_DIR/frontend/.env

# 一時ファイルを削除
rm -f /tmp/backend.env /tmp/frontend.env

# 5. リモートサーバーでのセットアップ
print_info "リモートサーバーでセットアップを実行中..."
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST << EOF
    # バックエンドのセットアップ
    cd $REMOTE_DIR/backend
    
    # Composer依存関係のインストール
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader
    else
        echo "Composerがインストールされていません。手動でインストールしてください。"
    fi
    
    # Laravel設定
    if command -v php &> /dev/null; then
        php artisan key:generate
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan migrate --force
    else
        echo "PHPがインストールされていません。手動でインストールしてください。"
    fi
    
    # フロントエンドのビルド
    cd $REMOTE_DIR/frontend
    
    if command -v npm &> /dev/null; then
        npm ci --only=production
        npm run build
    else
        echo "Node.js/npmがインストールされていません。手動でインストールしてください。"
    fi
    
    # 権限設定
    chmod -R 755 $REMOTE_DIR
    chmod -R 644 $REMOTE_DIR/backend/.env
    chmod -R 644 $REMOTE_DIR/frontend/.env
    
    echo "セットアップが完了しました"
EOF

print_success "=== SSHデプロイ完了 ==="
print_info ""
print_info "🎉 アプリケーションが正常にデプロイされました！"
print_info ""
print_info "📱 アクセス情報:"
print_info "   URL: $APP_URL"
print_info "   サーバー: $SSH_HOST"
print_info ""
print_info "📋 次のステップ:"
print_info "   1. Webサーバー（Nginx/Apache）の設定"
print_info "   2. SSL証明書の設定"
print_info "   3. アプリケーションの動作確認"
print_info "   4. データベース接続の確認"
print_info ""
print_info "🔧 手動設定が必要な場合:"
print_info "   - Webサーバーの設定"
print_info "   - SSL証明書の設定"
print_info "   - ファイアウォールの設定"
print_info "   - ドメインの設定"
print_info ""
print_info "🚀 アプリケーションが正常に動作していることを確認してください！"
