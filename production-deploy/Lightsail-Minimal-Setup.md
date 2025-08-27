# Lightsail 最小限セットアップコマンド

## 概要
AWS Lightsailは既にApache + PHP + MySQLがインストールされた環境です。
RDS Aurora PostgreSQLを使用するため、ローカルPostgreSQLは不要です。
既存のPHPに必要な拡張機能のみを追加インストールします。

## 🔧 前提条件確認

```bash
# 既存環境の確認
php --version
apache2 -v
```

## 📦 必要なPHP拡張機能の追加インストール

```bash
# システム更新
sudo apt-get update

# PHP拡張機能（PostgreSQL接続用）
sudo apt-get install -y php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath

# Composer
sudo apt-get install -y composer

# Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# PostgreSQLクライアント（接続テスト用）
sudo apt-get install -y postgresql-client
```

## 🗄️ RDS Aurora PostgreSQL設定

```bash
# RDS Aurora接続テスト
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com -U evox_user -d evox_production
```

## 📁 アプリケーションディレクトリ準備

```bash
# ディレクトリ作成
sudo mkdir -p /var/www/evox
sudo chown -R $USER:$USER /var/www/evox
```

## 🌐 Apache設定

```bash
# 必要なモジュール有効化
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite

# 仮想ホスト設定
sudo tee /etc/apache2/sites-available/evox.conf << 'EOF'
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/evox/frontend/dist
    
    <Directory /var/www/evox/frontend/dist>
        AllowOverride All
        Require all granted
        FallbackResource /index.html
    </Directory>
    
    ProxyPreserveHost On
    ProxyPass /api http://127.0.0.1:8000/api
    ProxyPassReverse /api http://127.0.0.1:8000/api
    
    ErrorLog ${APACHE_LOG_DIR}/evox_error.log
    CustomLog ${APACHE_LOG_DIR}/evox_access.log combined
</VirtualHost>
EOF

# デフォルトサイト無効化
sudo a2dissite 000-default.conf

# EvoXサイト有効化
sudo a2ensite evox.conf

# Apache再起動
sudo systemctl restart apache2
```

## 🚀 アプリケーションデプロイ

### バックエンドセットアップ
```bash
cd /var/www/evox/backend

# Composer依存関係
composer install --no-dev --optimize-autoloader

# Laravel設定
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

### フロントエンドビルド
```bash
cd /var/www/evox/frontend

# 依存関係インストール
npm ci --only=production

# ビルド
npm run build
```

### 権限設定
```bash
sudo chown -R www-data:www-data /var/www/evox
sudo chmod -R 755 /var/www/evox
```

## 🔄 アプリケーションサービス設定

```bash
# systemdサービス作成
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

[Install]
WantedBy=multi-user.target
EOF

# サービス有効化
sudo systemctl daemon-reload
sudo systemctl enable evox-backend
sudo systemctl start evox-backend
```

## 🔐 SSL証明書設定

```bash
# Certbotインストール
sudo apt-get install -y certbot python3-certbot-apache

# SSL証明書取得
sudo certbot --apache -d your-domain.com --non-interactive --agree-tos --email admin@your-domain.com
```

## 📊 動作確認

```bash
# サービス確認
sudo systemctl status apache2 evox-backend

# アプリケーション確認
curl http://localhost/api/health

# RDS接続確認
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com -U evox_user -d evox_production -c "SELECT version();"
```

## 🔧 トラブルシューティング

```bash
# ログ確認
sudo tail -f /var/log/apache2/evox_error.log
sudo journalctl -u evox-backend -f

# 権限修正
sudo chown -R www-data:www-data /var/www/evox

# RDS接続テスト
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com -U evox_user -d evox_production
```
