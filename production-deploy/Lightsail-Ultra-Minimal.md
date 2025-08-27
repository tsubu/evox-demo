# Lightsail 超最小限セットアップ

## 概要
Lightsailは既にApache + PHP + MySQLがインストール済みです。
EvoXアプリケーションに必要な最小限の追加のみを行います。

## 🚀 超最小限セットアップ（ワンライナー）

```bash
# 1. PHP拡張機能 + Composer + Node.js + PostgreSQLクライアント
sudo apt-get update && sudo apt-get install -y php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath composer postgresql-client && curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash - && sudo apt-get install -y nodejs

# 2. ディレクトリ作成
sudo mkdir -p /var/www/evox && sudo chown -R $USER:$USER /var/www/evox

# 3. Apache設定
sudo a2enmod proxy proxy_http rewrite && sudo a2dissite 000-default.conf && sudo a2ensite evox.conf && sudo systemctl restart apache2

# 4. サービス設定
sudo systemctl daemon-reload && sudo systemctl enable evox-backend
```

## 📦 インストールするもの（最小限）

### 必須
- `php8.1-pgsql` - PostgreSQL接続用
- `php8.1-mbstring` - マルチバイト文字列処理
- `php8.1-xml` - XML処理
- `php8.1-curl` - HTTP通信
- `php8.1-zip` - ファイル圧縮
- `php8.1-gd` - 画像処理
- `php8.1-bcmath` - 高精度数学演算
- `composer` - PHP依存関係管理
- `nodejs` - フロントエンドビルド用
- `postgresql-client` - RDS接続テスト用

### 不要（既にインストール済み）
- ❌ Apache（既にインストール済み）
- ❌ PHP（既にインストール済み）
- ❌ MySQL（既にインストール済み）
- ❌ PostgreSQL（RDS Auroraを使用）

## 🌐 Apache設定（最小限）

```bash
# 仮想ホスト設定ファイル作成
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
```

## 🔄 サービス設定（最小限）

```bash
# systemdサービスファイル作成
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
```

## 🚀 アプリケーションデプロイ（最小限）

```bash
# バックエンド
cd /var/www/evox/backend && composer install --no-dev --optimize-autoloader && php artisan key:generate && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force

# フロントエンド
cd /var/www/evox/frontend && npm ci --only=production && npm run build

# 権限設定
sudo chown -R www-data:www-data /var/www/evox
```

## 🗄️ RDS Aurora接続（最小限）

```bash
# 接続テスト
psql -h your-aurora-endpoint -U evox_user -d evox_production

# 動作確認
psql -h your-aurora-endpoint -U evox_user -d evox_production -c "SELECT version();"
```

## 📊 動作確認（最小限）

```bash
# サービス確認
sudo systemctl status apache2 evox-backend

# アプリケーション確認
curl http://localhost/api/health
```

## 🎯 超最小限セットアップの流れ

1. **RDS Aurora PostgreSQLクラスター作成**（AWSコンソール）
2. **Lightsail環境セットアップ**（上記ワンライナー）
3. **SSHデプロイスクリプトでアプリケーション転送**
4. **動作確認**

## 💡 ポイント

- **Lightsailは既に最適化済み**
- **追加インストールは最小限**
- **RDS Auroraでデータベース管理**
- **セキュリティはLightsailが管理**
- **ファイアウォール設定不要**





