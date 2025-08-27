# Lightsail 必要最小限コマンド集（RDS Aurora使用）

## 概要
AWS Lightsailは既にApache + PHP + MySQLがインストール済みです。
RDS Aurora PostgreSQLを使用するため、ローカルPostgreSQLは不要です。
既存のPHPに必要な拡張機能のみを追加インストールします。

## 🚀 ワンライナーセットアップ

```bash
# Lightsailコンソールで実行
wget -O setup.sh https://raw.githubusercontent.com/your-repo/evox/main/production-deploy/Lightsail-One-Line-Setup.sh && chmod +x setup.sh && ./setup.sh
```

## 📦 必要なPHP拡張機能のみ

```bash
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

# データベース作成（初回のみ）
psql -h your-aurora-endpoint -U evox_user -d postgres -c "CREATE DATABASE evox_production;"
```

## 📁 ディレクトリ作成

```bash
sudo mkdir -p /var/www/evox
sudo chown -R $USER:$USER /var/www/evox
```

## 🌐 Apache設定（最小限）

```bash
# モジュール有効化
sudo a2enmod proxy proxy_http rewrite
sudo a2dissite 000-default.conf

# 仮想ホスト設定
sudo tee /etc/apache2/sites-available/evox.conf << 'EOF'
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/evox/frontend/dist
    
    <Directory /var/www/evox/frontend/dist>
        AllowOverride All
        Require all granted
        FallbackResource /index.html
    </Directory>
    
    ProxyPreserveHost On
    ProxyPass /api http://127.0.0.1:8000/api
    ProxyPassReverse /api http://127.0.0.1:8000/api
</VirtualHost>
EOF

sudo a2ensite evox.conf
sudo systemctl restart apache2
```

## 🚀 アプリケーションデプロイ

### バックエンド
```bash
cd /var/www/evox/backend
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

### フロントエンド
```bash
cd /var/www/evox/frontend
npm ci --only=production
npm run build
```

### 権限設定
```bash
sudo chown -R www-data:www-data /var/www/evox
```

## 🔄 サービス設定

```bash
# サービスファイル作成
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

sudo systemctl daemon-reload
sudo systemctl enable evox-backend
sudo systemctl start evox-backend
```

## 🔐 SSL証明書（オプション）

```bash
sudo apt-get install -y certbot python3-certbot-apache
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

## 📋 完全なセットアップ手順

1. **RDS Aurora PostgreSQLクラスターを作成**
2. **Lightsailコンソールでワンライナー実行**
3. **データベース接続情報を.envファイルに設定**
4. **SSHデプロイスクリプトでアプリケーション転送**
5. **ドメイン設定（オプション）**
6. **SSL証明書取得（オプション）**

## 🎯 重要なポイント

- **Lightsailは既にApache + PHP + MySQLがインストール済み**
- **RDS Aurora PostgreSQLを使用（ローカルPostgreSQL不要）**
- **PHP拡張機能のみ追加インストールが必要**
- **Node.jsとComposerのみ追加インストールが必要**
- **ファイアウォール設定は不要（Lightsailが管理）**
- **基本的なセキュリティはLightsailが提供**
- **データベース管理はRDSが担当**
