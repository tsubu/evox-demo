# Lightsail セットアップガイド

## 概要
AWS Lightsail環境でのEvoXアプリケーションセットアップ手順です。

## 前提条件
- Lightsailインスタンス（Ubuntu 22.04推奨）
- Apache + PHP 8.3.23以上
- AWS RDS Aurora PostgreSQL（外部データベース）
- SSHアクセス可能
- コンソールアクセス可能

## 🔧 1. システムパッケージの更新

```bash
# システムパッケージを更新
sudo apt-get update
sudo apt-get upgrade -y

# 必要なパッケージをインストール（PostgreSQLは除外）
sudo apt-get install -y \
    apache2 \
    php8.3 \
    php8.3-fpm \
    php8.3-pgsql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-bcmath \
    composer \
    git \
    unzip
```

## 🗄️ 2. Aurora PostgreSQL接続設定

```bash
# データベース接続設定（.envファイルで設定）
# DB_CONNECTION=pgsql
# DB_HOST=your-aurora-cluster-endpoint.cluster-xxxxx.region.rds.amazonaws.com
# DB_PORT=5432
# DB_DATABASE=evox_production
# DB_USERNAME=evox_user
# DB_PASSWORD=your-secure-password

# 接続テスト（オプション）
# psql -h your-aurora-cluster-endpoint.cluster-xxxxx.region.rds.amazonaws.com -U evox_user -d evox_production
```

## 📁 3. アプリケーションディレクトリの準備

```bash
# アプリケーションディレクトリを作成
sudo mkdir -p /var/www/evox
sudo chown -R $USER:$USER /var/www/evox

# 権限を設定
sudo chmod -R 755 /var/www/evox
```

## 🌐 4. Apache設定

```bash
# Apache仮想ホスト設定を作成
sudo tee /etc/apache2/sites-available/evox.conf << 'EOF'
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/evox/frontend/dist
    
    # フロントエンド
    <Directory /var/www/evox/frontend/dist>
        AllowOverride All
        Require all granted
        FallbackResource /index.html
    </Directory>
    
    # バックエンドAPI
    ProxyPreserveHost On
    ProxyPass /api http://127.0.0.1:8000/api
    ProxyPassReverse /api http://127.0.0.1:8000/api
    
    # 静的ファイル
    Alias /images /var/www/evox/frontend/dist/images
    <Directory /var/www/evox/frontend/dist/images>
        Require all granted
        ExpiresActive On
        ExpiresDefault "access plus 1 year"
    </Directory>
    
    # ログ設定
    ErrorLog ${APACHE_LOG_DIR}/evox_error.log
    CustomLog ${APACHE_LOG_DIR}/evox_access.log combined
</VirtualHost>
EOF

# 必要なApacheモジュールを有効化
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod expires

# デフォルトサイトを無効化
sudo a2dissite 000-default.conf

# EvoXサイトを有効化
sudo a2ensite evox.conf

# Apache設定をテスト
sudo apache2ctl configtest

# Apacheを再起動
sudo systemctl restart apache2
```

## 📦 5. Node.jsのインストール

```bash
# Node.js 18.xをインストール
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# バージョン確認
node --version
npm --version
```

## 🔐 6. SSL証明書の設定（Let's Encrypt）

```bash
# Certbotをインストール
sudo apt-get install -y certbot python3-certbot-apache

# SSL証明書を取得
sudo certbot --apache -d your-domain.com --non-interactive --agree-tos --email admin@your-domain.com

# 自動更新の設定
sudo crontab -e
# 以下を追加
0 12 * * * /usr/bin/certbot renew --quiet
```

## 🚀 7. アプリケーションのデプロイ

### 方法1: SSHデプロイスクリプトを使用
```bash
# ローカルマシンから実行
cd production-deploy/aws-deploy
./simple-deploy.sh
```

### 方法2: 手動デプロイ
```bash
# ファイルを転送後、サーバー上で実行
cd /var/www/evox/backend

# Composer依存関係をインストール
composer install --no-dev --optimize-autoloader

# Laravel設定
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# フロントエンドビルド
cd /var/www/evox/frontend
npm ci --only=production
npm run build

# 権限設定
sudo chown -R www-data:www-data /var/www/evox
sudo chmod -R 755 /var/www/evox
sudo chmod -R 644 /var/www/evox/backend/.env
sudo chmod -R 644 /var/www/evox/frontend/.env
```

## 🔄 8. アプリケーションサービスの設定

```bash
# systemdサービスファイルを作成
sudo tee /etc/systemd/system/evox-backend.service << 'EOF'
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
RestartSec=3

[Install]
WantedBy=multi-user.target
EOF

# サービスを有効化して起動
sudo systemctl daemon-reload
sudo systemctl enable evox-backend
sudo systemctl start evox-backend

# ステータス確認
sudo systemctl status evox-backend
```

## 🛡️ 9. ファイアウォール設定

```bash
# UFWを有効化
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw --force enable

# ステータス確認
sudo ufw status
```

## 📊 10. ログの確認

```bash
# Apacheログ
sudo tail -f /var/log/apache2/evox_access.log
sudo tail -f /var/log/apache2/evox_error.log

# アプリケーションログ
sudo tail -f /var/www/evox/backend/storage/logs/laravel.log

# システムログ
sudo journalctl -u evox-backend -f
```

## 🔧 11. トラブルシューティング

### よくある問題と解決方法

#### Apacheエラー
```bash
# Apache設定テスト
sudo apache2ctl configtest

# Apache再起動
sudo systemctl restart apache2
```

#### アプリケーションエラー
```bash
# サービスステータス確認
sudo systemctl status evox-backend

# サービス再起動
sudo systemctl restart evox-backend

# ログ確認
sudo journalctl -u evox-backend -n 50
```

#### データベース接続エラー
```bash
# PostgreSQLステータス確認
sudo systemctl status postgresql

# 接続テスト
psql -h localhost -U evox_user -d evox_production
```

#### 権限エラー
```bash
# 権限修正
sudo chown -R www-data:www-data /var/www/evox
sudo chmod -R 755 /var/www/evox
sudo chmod -R 644 /var/www/evox/backend/.env
```

## 📋 12. メンテナンスコマンド

### アプリケーション更新
```bash
# サービス停止
sudo systemctl stop evox-backend

# ファイル更新
# （SSHデプロイスクリプトまたは手動でファイルを転送）

# 依存関係更新
cd /var/www/evox/backend
composer install --no-dev --optimize-autoloader

# フロントエンドビルド
cd /var/www/evox/frontend
npm ci --only=production
npm run build

# キャッシュクリア
cd /var/www/evox/backend
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# サービス再起動
sudo systemctl start evox-backend
```

### データベースバックアップ
```bash
# バックアップ作成
pg_dump -h localhost -U evox_user evox_production > backup_$(date +%Y%m%d_%H%M%S).sql

# バックアップ復元
psql -h localhost -U evox_user evox_production < backup_file.sql
```

## 🎯 13. 動作確認

### アプリケーションアクセス
```bash
# ローカルテスト
curl http://localhost/api/health

# 外部アクセス
curl https://your-domain.com/api/health
```

### サービス確認
```bash
# 全サービスのステータス確認
sudo systemctl status apache2
sudo systemctl status postgresql
sudo systemctl status evox-backend
```

## 📞 サポート

問題が発生した場合は以下を確認してください：
1. サービスログ
2. Apacheログ
3. アプリケーションログ
4. ファイアウォール設定
5. SSL証明書の有効性
