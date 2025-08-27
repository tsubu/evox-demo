#!/bin/bash

# 本番環境用ファイル準備スクリプト
# 使用方法: ./prepare-production.sh

set -e

echo "=== 本番環境用ファイル準備開始 ==="

# 作業ディレクトリを設定
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
PROD_DIR="$SCRIPT_DIR"

echo "プロジェクトルート: $PROJECT_ROOT"
echo "本番環境ディレクトリ: $PROD_DIR"

# 1. バックエンドファイルをコピー
echo ""
echo "📁 バックエンドファイルをコピー中..."
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
    "$PROJECT_ROOT/backend/" "$PROD_DIR/backend/"

# 2. フロントエンドファイルをコピー
echo ""
echo "📁 フロントエンドファイルをコピー中..."
rsync -av --exclude='node_modules/' \
    --exclude='dist/' \
    --exclude='.env' \
    --exclude='.git/' \
    --exclude='*.log' \
    "$PROJECT_ROOT/frontend/" "$PROD_DIR/frontend/"

# 3. 本番環境用設定ファイルを作成
echo ""
echo "⚙️  本番環境用設定ファイルを作成中..."

# バックエンド用.env.production
cat > "$PROD_DIR/backend/.env.production" << 'EOF'
APP_NAME=EvoX
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=your-rds-endpoint
DB_PORT=5432
DB_DATABASE=evox_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=your-redis-endpoint
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=your_twilio_account_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_FROM_NUMBER=your_twilio_phone_number

AWS_ACCESS_KEY_ID=your_aws_access_key
AWS_SECRET_ACCESS_KEY=your_aws_secret_key
AWS_DEFAULT_REGION=ap-northeast-1
AWS_BUCKET=your-s3-bucket
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF

# フロントエンド用.env.production
cat > "$PROD_DIR/frontend/.env.production" << 'EOF'
VITE_APP_NAME=EvoX
VITE_API_BASE_URL=https://your-domain.com/api
VITE_APP_URL=https://your-domain.com
EOF

# 4. Docker設定ファイルを作成
echo ""
echo "🐳 Docker設定ファイルを作成中..."

# docker-compose.production.yml
cat > "$PROD_DIR/docker-compose.production.yml" << 'EOF'
version: '3.8'

services:
  # PostgreSQL Database
  postgres:
    image: postgres:15
    container_name: evox_postgres
    environment:
      POSTGRES_DB: evox_production
      POSTGRES_USER: evox_user
      POSTGRES_PASSWORD: your_db_password
    volumes:
      - postgres_data:/var/lib/postgresql/data
    ports:
      - "5432:5432"
    networks:
      - evox_network

  # Redis Cache
  redis:
    image: redis:7-alpine
    container_name: evox_redis
    command: redis-server --requirepass your_redis_password
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"
    networks:
      - evox_network

  # Laravel Backend
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile.production
    container_name: evox_backend
    environment:
      - APP_ENV=production
    volumes:
      - ./backend:/var/www/html
      - ./backend/storage:/var/www/html/storage
    ports:
      - "8000:8000"
    depends_on:
      - postgres
      - redis
    networks:
      - evox_network

  # Vue.js Frontend
  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile.production
    container_name: evox_frontend
    ports:
      - "3000:3000"
    depends_on:
      - backend
    networks:
      - evox_network

  # Nginx Reverse Proxy
  nginx:
    image: nginx:alpine
    container_name: evox_nginx
    volumes:
      - ./config/nginx.conf:/etc/nginx/nginx.conf
      - ./frontend/dist:/usr/share/nginx/html
    ports:
      - "80:80"
      - "443:443"
    depends_on:
      - frontend
      - backend
    networks:
      - evox_network

volumes:
  postgres_data:
  redis_data:

networks:
  evox_network:
    driver: bridge
EOF

# 5. 本番環境用Dockerfileを作成
echo ""
echo "🐳 本番環境用Dockerfileを作成中..."

# バックエンド用Dockerfile.production
cat > "$PROD_DIR/backend/Dockerfile.production" << 'EOF'
FROM php:8.2-fpm

# システムパッケージをインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# 依存関係をコピーしてインストール
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# アプリケーションファイルをコピー
COPY . .

# 権限を設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# 本番環境用設定をコピー
COPY .env.production .env

# アプリケーションキーを生成
RUN php artisan key:generate

# データベースマイグレーションを実行
RUN php artisan migrate --force

# キャッシュを最適化
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# PHP-FPMを起動
CMD ["php-fpm"]
EOF

# フロントエンド用Dockerfile.production
cat > "$PROD_DIR/frontend/Dockerfile.production" << 'EOF'
FROM node:18-alpine

# 作業ディレクトリを設定
WORKDIR /app

# 依存関係をコピーしてインストール
COPY package*.json ./
RUN npm ci --only=production

# アプリケーションファイルをコピー
COPY . .

# 本番環境用設定をコピー
COPY .env.production .env

# 本番用ビルドを実行
RUN npm run build

# 本番用サーバーを起動
CMD ["npm", "run", "preview"]
EOF

# 6. Nginx設定ファイルを作成
echo ""
echo "🌐 Nginx設定ファイルを作成中..."

mkdir -p "$PROD_DIR/config"
cat > "$PROD_DIR/config/nginx.conf" << 'EOF'
events {
    worker_connections 1024;
}

http {
    upstream backend {
        server backend:8000;
    }

    upstream frontend {
        server frontend:3000;
    }

    server {
        listen 80;
        server_name your-domain.com;

        # フロントエンド
        location / {
            proxy_pass http://frontend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # バックエンドAPI
        location /api {
            proxy_pass http://backend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # 静的ファイル
        location /images {
            alias /usr/share/nginx/html/images;
            expires 1y;
            add_header Cache-Control "public, immutable";
        }
    }
}
EOF

# 7. デプロイスクリプトを作成
echo ""
echo "📦 デプロイスクリプトを作成中..."

cat > "$PROD_DIR/deploy.sh" << 'EOF'
#!/bin/bash

# 本番環境デプロイスクリプト
set -e

echo "=== 本番環境デプロイ開始 ==="

# 環境変数を読み込み
source .env.production

# 1. 依存関係をインストール
echo "📦 依存関係をインストール中..."
cd backend && composer install --no-dev --optimize-autoloader
cd ../frontend && npm ci --only=production

# 2. フロントエンドをビルド
echo "🔨 フロントエンドをビルド中..."
npm run build

# 3. バックエンドの最適化
echo "⚡ バックエンドを最適化中..."
cd ../backend
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. データベースマイグレーション
echo "🗄️  データベースマイグレーション中..."
php artisan migrate --force

# 5. 権限を設定
echo "🔐 権限を設定中..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ デプロイ完了！"
EOF

chmod +x "$PROD_DIR/deploy.sh"

# 8. READMEファイルを作成
echo ""
echo "📖 READMEファイルを作成中..."

cat > "$PROD_DIR/README.md" << 'EOF'
# EvoX 本番環境デプロイ

## 概要
このディレクトリには本番環境用のファイルが含まれています。

## ファイル構成
```
production-deploy/
├── backend/                 # Laravelバックエンド
├── frontend/               # Vue.jsフロントエンド
├── config/                 # 設定ファイル
├── aws-deploy/            # AWSデプロイ用スクリプト
├── scripts/               # ユーティリティスクリプト
├── docker-compose.production.yml
├── deploy.sh
└── README.md
```

## セットアップ手順

### 1. 環境変数の設定
```bash
# バックエンド
cp backend/.env.production backend/.env
# フロントエンド
cp frontend/.env.production frontend/.env
```

### 2. 設定値の更新
各.envファイルの以下の値を実際の値に更新してください：
- データベース接続情報
- Twilio認証情報
- AWS認証情報
- ドメイン名

### 3. Docker Composeで起動
```bash
docker-compose -f docker-compose.production.yml up -d
```

### 4. 手動デプロイ
```bash
./deploy.sh
```

## AWSデプロイ
AWSデプロイ用のスクリプトは `aws-deploy/` ディレクトリにあります。

## 注意事項
- 本番環境では必ずHTTPSを使用してください
- データベースのバックアップを定期的に実行してください
- ログファイルのローテーションを設定してください
EOF

echo ""
echo "✅ 本番環境用ファイル準備完了！"
echo ""
echo "📁 作成されたファイル:"
echo "  - バックエンド: $PROD_DIR/backend/"
echo "  - フロントエンド: $PROD_DIR/frontend/"
echo "  - Docker設定: docker-compose.production.yml"
echo "  - Nginx設定: config/nginx.conf"
echo "  - デプロイスクリプト: deploy.sh"
echo "  - README: README.md"
echo ""
echo "次のステップ:"
echo "1. 各.envファイルの設定値を更新"
echo "2. AWSデプロイスクリプトを実行"
echo "3. 本番環境でテスト"
