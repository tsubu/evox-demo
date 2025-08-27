#!/bin/bash

# AWS Lightsail + RDS (Aurora PostgreSQL) ワンクリックデプロイスクリプト
# 使用方法: ./deploy-to-lightsail.sh

set -e

# 色付き出力用の関数
print_info() {
    echo -e "\033[1;34mℹ️  $1\033[0m"
}

print_success() {
    echo -e "\033[1;32m✅ $1\033[0m"
}

print_warning() {
    echo -e "\033[1;33m⚠️  $1\033[0m"
}

print_error() {
    echo -e "\033[1;31m❌ $1\033[0m"
}

# 設定ファイルのパス
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROD_DIR="$(dirname "$SCRIPT_DIR")"
CONFIG_FILE="$SCRIPT_DIR/aws-config.sh"

print_info "=== AWS Lightsail + RDS ワンクリックデプロイ開始 ==="
print_info "スクリプトディレクトリ: $SCRIPT_DIR"
print_info "本番環境ディレクトリ: $PROD_DIR"

# 設定ファイルの存在確認
if [ ! -f "$CONFIG_FILE" ]; then
    print_error "設定ファイルが見つかりません: $CONFIG_FILE"
    print_info "aws-config.sh.exampleをコピーして設定してください"
    print_info "cp aws-config.sh.example aws-config.sh"
    exit 1
fi

# 設定ファイルを読み込み
source "$CONFIG_FILE"

# 必要なツールの確認
check_requirements() {
    print_info "必要なツールを確認中..."
    
    # AWS CLI
    if ! command -v aws &> /dev/null; then
        print_error "AWS CLIがインストールされていません"
        print_info "https://aws.amazon.com/cli/ からインストールしてください"
        exit 1
    fi
    
    # SSH
    if ! command -v ssh &> /dev/null; then
        print_error "SSHがインストールされていません"
        exit 1
    fi
    
    # SCP
    if ! command -v scp &> /dev/null; then
        print_error "SCPがインストールされていません"
        exit 1
    fi
    
    print_success "必要なツールが確認されました"
}

# AWS認証情報の確認
check_aws_credentials() {
    print_info "AWS認証情報を確認中..."
    
    if ! aws sts get-caller-identity &> /dev/null; then
        print_error "AWS認証情報が設定されていません"
        print_info "以下のコマンドで設定してください:"
        print_info "aws configure"
        exit 1
    fi
    
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    print_success "AWS認証情報が確認されました (Account: $AWS_ACCOUNT_ID)"
}

# Lightsailインスタンスの作成
create_lightsail_instance() {
    print_info "Lightsailインスタンスを作成中..."
    
    # インスタンスの存在確認
    if aws lightsail get-instance --instance-name $LIGHTSAIL_INSTANCE_NAME &> /dev/null; then
        print_info "Lightsailインスタンス '$LIGHTSAIL_INSTANCE_NAME' は既に存在します"
        return
    fi
    
    # インスタンスを作成
    aws lightsail create-instances \
        --instance-names $LIGHTSAIL_INSTANCE_NAME \
        --availability-zone $LIGHTSAIL_INSTANCE_ZONE \
        --blueprint-id $LIGHTSAIL_INSTANCE_BLUEPRINT \
        --bundle-id $LIGHTSAIL_INSTANCE_BUNDLE
    
    print_success "Lightsailインスタンス '$LIGHTSAIL_INSTANCE_NAME' を作成しました"
    
    # インスタンスが起動するまで待機
    print_info "インスタンスの起動を待機中..."
    aws lightsail wait instance-running --instance-name $LIGHTSAIL_INSTANCE_NAME
    print_success "インスタンスが起動しました"
}

# RDS Auroraクラスターの作成
create_rds_cluster() {
    print_info "RDS Auroraクラスターを作成中..."
    
    # クラスターの存在確認
    if aws rds describe-db-clusters --db-cluster-identifier $RDS_CLUSTER_IDENTIFIER &> /dev/null; then
        print_info "RDSクラスター '$RDS_CLUSTER_IDENTIFIER' は既に存在します"
        return
    fi
    
    # サブネットグループの作成
    aws rds create-db-subnet-group \
        --db-subnet-group-name evox-subnet-group \
        --db-subnet-group-description "EvoX production subnet group" \
        --subnet-ids $SUBNET_IDS
    
    # クラスターを作成
    aws rds create-db-cluster \
        --db-cluster-identifier $RDS_CLUSTER_IDENTIFIER \
        --engine $RDS_ENGINE \
        --engine-version $RDS_ENGINE_VERSION \
        --master-username $RDS_MASTER_USERNAME \
        --master-user-password $RDS_MASTER_PASSWORD \
        --db-subnet-group-name evox-subnet-group \
        --vpc-security-group-ids $SECURITY_GROUP_IDS
    
    # インスタンスを作成
    aws rds create-db-instance \
        --db-instance-identifier $RDS_CLUSTER_IDENTIFIER-instance-1 \
        --db-cluster-identifier $RDS_CLUSTER_IDENTIFIER \
        --engine $RDS_ENGINE \
        --db-instance-class $RDS_INSTANCE_CLASS
    
    print_success "RDS Auroraクラスター '$RDS_CLUSTER_IDENTIFIER' を作成しました"
    
    # クラスターが利用可能になるまで待機
    print_info "RDSクラスターの起動を待機中..."
    aws rds wait db-cluster-available --db-cluster-identifier $RDS_CLUSTER_IDENTIFIER
    print_success "RDSクラスターが利用可能になりました"
}

# 本番環境用ファイルを準備
prepare_production_files() {
    print_info "本番環境用ファイルを準備中..."
    
    # 本番環境用ディレクトリを作成
    mkdir -p "$SCRIPT_DIR/temp-deploy"
    
    # バックエンドファイルをコピー
    rsync -av --exclude='vendor/' \
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
        "$PROD_DIR/backend/" "$SCRIPT_DIR/temp-deploy/backend/"
    
    # フロントエンドファイルをコピー
    rsync -av --exclude='node_modules/' \
        --exclude='dist/' \
        --exclude='.env' \
        --exclude='.git/' \
        --exclude='*.log' \
        "$PROD_DIR/frontend/" "$SCRIPT_DIR/temp-deploy/frontend/"
    
    # 本番環境用.envファイルを作成
    cat > "$SCRIPT_DIR/temp-deploy/backend/.env" << EOF
APP_NAME=EvoX
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$DOMAIN_NAME

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=$RDS_ENDPOINT
DB_PORT=5432
DB_DATABASE=$RDS_DATABASE_NAME
DB_USERNAME=$RDS_MASTER_USERNAME
DB_PASSWORD=$RDS_MASTER_PASSWORD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=$MAIL_HOST
MAIL_PORT=$MAIL_PORT
MAIL_USERNAME=$MAIL_USERNAME
MAIL_PASSWORD=$MAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=$MAIL_FROM_ADDRESS
MAIL_FROM_NAME="\${APP_NAME}"

SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=$TWILIO_ACCOUNT_SID
TWILIO_AUTH_TOKEN=$TWILIO_AUTH_TOKEN
TWILIO_FROM_NUMBER=$TWILIO_FROM_NUMBER

AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY
AWS_DEFAULT_REGION=$AWS_REGION
AWS_BUCKET=$S3_BUCKET
AWS_USE_PATH_STYLE_ENDPOINT=false
EOF
    
    # フロントエンド用.envファイルを作成
    cat > "$SCRIPT_DIR/temp-deploy/frontend/.env" << EOF
VITE_APP_NAME=EvoX
VITE_API_BASE_URL=https://$DOMAIN_NAME/api
VITE_APP_URL=https://$DOMAIN_NAME
EOF
    
    print_success "本番環境用ファイルを準備しました"
}

# Lightsailインスタンスにデプロイ
deploy_to_lightsail() {
    print_info "Lightsailインスタンスにデプロイ中..."
    
    # インスタンスのIPアドレスを取得
    INSTANCE_IP=$(aws lightsail get-instance --instance-name $LIGHTSAIL_INSTANCE_NAME --query 'instance.publicIpAddress' --output text)
    print_info "インスタンスIP: $INSTANCE_IP"
    
    # SSHキーペアの設定確認
    SSH_KEY_NAME="evox-production-key"
    if ! aws lightsail get-key-pair --key-pair-name $SSH_KEY_NAME &> /dev/null; then
        print_info "SSHキーペアを作成中..."
        aws lightsail create-key-pair --key-pair-name $SSH_KEY_NAME
        aws lightsail download-default-key-pair --key-pair-name $SSH_KEY_NAME
        chmod 400 "$SSH_KEY_NAME.pem"
    fi
    
    # インスタンスにファイルを転送
    print_info "ファイルを転送中..."
    scp -i "$SSH_KEY_NAME.pem" -r "$SCRIPT_DIR/temp-deploy/" ubuntu@$INSTANCE_IP:/home/ubuntu/evox/
    
    # インスタンス上でセットアップを実行
    print_info "インスタンス上でセットアップを実行中..."
    ssh -i "$SSH_KEY_NAME.pem" ubuntu@$INSTANCE_IP << 'EOF'
        # システムパッケージを更新
        sudo apt-get update
        sudo apt-get upgrade -y
        
        # 必要なパッケージをインストール
        sudo apt-get install -y nginx php8.1-fpm php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip composer nodejs npm git
        
        # Node.jsの最新版をインストール
        curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
        sudo apt-get install -y nodejs
        
        # アプリケーションディレクトリを作成
        sudo mkdir -p /var/www/evox
        sudo chown -R ubuntu:ubuntu /var/www/evox
        
        # バックエンドをセットアップ
        cd /home/ubuntu/evox/backend
        composer install --no-dev --optimize-autoloader
        php artisan key:generate
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan migrate --force
        
        # フロントエンドをビルド
        cd /home/ubuntu/evox/frontend
        npm ci --only=production
        npm run build
        
        # ファイルを本番ディレクトリに移動
        sudo cp -r /home/ubuntu/evox/backend/* /var/www/evox/backend/
        sudo cp -r /home/ubuntu/evox/frontend/dist/* /var/www/evox/frontend/
        
        # 権限を設定
        sudo chown -R www-data:www-data /var/www/evox
        sudo chmod -R 755 /var/www/evox
        
        # Nginx設定を作成
        sudo tee /etc/nginx/sites-available/evox << 'NGINX_CONFIG'
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/evox/frontend;
    index index.html;
    
    # フロントエンド
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # バックエンドAPI
    location /api {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # 静的ファイル
    location /images {
        alias /var/www/evox/frontend/images;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
NGINX_CONFIG
        
        # Nginxサイトを有効化
        sudo ln -sf /etc/nginx/sites-available/evox /etc/nginx/sites-enabled/
        sudo rm -f /etc/nginx/sites-enabled/default
        sudo nginx -t
        sudo systemctl reload nginx
        
        # PHP-FPMを設定
        sudo systemctl enable php8.1-fpm
        sudo systemctl start php8.1-fpm
        
        # アプリケーションサービスを作成
        sudo tee /etc/systemd/system/evox-backend.service << 'SERVICE_CONFIG'
[Unit]
Description=EvoX Backend
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/evox/backend
ExecStart=/usr/bin/php artisan serve --host=127.0.0.1 --port=8000
Restart=always

[Install]
WantedBy=multi-user.target
SERVICE_CONFIG
        
        # サービスを有効化して起動
        sudo systemctl daemon-reload
        sudo systemctl enable evox-backend
        sudo systemctl start evox-backend
        
        # ファイアウォールを設定
        sudo ufw allow 22
        sudo ufw allow 80
        sudo ufw allow 443
        sudo ufw --force enable
EOF
    
    print_success "Lightsailインスタンスへのデプロイが完了しました"
}

# SSL証明書の設定
setup_ssl() {
    print_info "SSL証明書を設定中..."
    
    # インスタンスのIPアドレスを取得
    INSTANCE_IP=$(aws lightsail get-instance --instance-name $LIGHTSAIL_INSTANCE_NAME --query 'instance.publicIpAddress' --output text)
    
    # CertbotをインストールしてSSL証明書を取得
    ssh -i "$SSH_KEY_NAME.pem" ubuntu@$INSTANCE_IP << EOF
        sudo apt-get install -y certbot python3-certbot-nginx
        sudo certbot --nginx -d $DOMAIN_NAME --non-interactive --agree-tos --email admin@$DOMAIN_NAME
        sudo systemctl reload nginx
EOF
    
    print_success "SSL証明書を設定しました"
}

# メイン処理
main() {
    check_requirements
    check_aws_credentials
    create_lightsail_instance
    create_rds_cluster
    prepare_production_files
    deploy_to_lightsail
    setup_ssl
    
    # インスタンスのIPアドレスを取得
    INSTANCE_IP=$(aws lightsail get-instance --instance-name $LIGHTSAIL_INSTANCE_NAME --query 'instance.publicIpAddress' --output text)
    
    print_success "=== AWS Lightsail + RDS デプロイ完了 ==="
    print_info "アプリケーションURL: https://$DOMAIN_NAME"
    print_info "インスタンスIP: $INSTANCE_IP"
    print_info "Lightsailコンソール: https://console.aws.amazon.com/lightsail/home?region=$AWS_REGION"
    print_info "RDSコンソール: https://console.aws.amazon.com/rds/home?region=$AWS_REGION#database:id=$RDS_CLUSTER_IDENTIFIER"
    
    # 一時ファイルを削除
    rm -rf "$SCRIPT_DIR/temp-deploy"
}

# スクリプト実行
main "$@"
