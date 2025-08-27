#!/bin/bash

# Lightsail ワンライナーセットアップスクリプト（RDS Aurora使用）
# 使用方法: ./Lightsail-One-Line-Setup.sh

echo "=== Lightsail 最小限セットアップ開始（RDS Aurora使用） ==="

# 1. システム更新とPHP拡張機能インストール
echo "📦 PHP拡張機能をインストール中..."
sudo apt-get update
sudo apt-get install -y php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath composer postgresql-client

# 2. Node.jsインストール
echo "📦 Node.jsをインストール中..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# 3. アプリケーションディレクトリ作成
echo "📁 ディレクトリを作成中..."
sudo mkdir -p /var/www/evox
sudo chown -R $USER:$USER /var/www/evox

# 4. Apache設定
echo "🌐 Apacheを設定中..."
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
    
    ErrorLog ${APACHE_LOG_DIR}/evox_error.log
    CustomLog ${APACHE_LOG_DIR}/evox_access.log combined
</VirtualHost>
EOF

sudo a2ensite evox.conf
sudo systemctl restart apache2

# 5. アプリケーションサービス設定
echo "🔄 アプリケーションサービスを設定中..."
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

echo "✅ セットアップ完了！"
echo ""
echo "📋 次のステップ:"
echo "1. RDS Aurora PostgreSQLクラスターを作成"
echo "2. データベース接続情報を.envファイルに設定"
echo "3. SSHデプロイスクリプトでアプリケーションを転送"
echo "4. ドメイン名を設定"
echo "5. SSL証明書を取得"
echo ""
echo "🔧 確認コマンド:"
echo "sudo systemctl status apache2 evox-backend"
echo ""
echo "🗄️ RDS接続確認:"
echo "psql -h your-aurora-endpoint -U evox_user -d evox_production"
