# クイックセットアップコマンド（Aurora PostgreSQL使用版）

## 1. システムパッケージのインストール
```bash
sudo apt-get update && sudo apt-get upgrade -y
sudo apt-get install -y apache2 php8.3 php8.3-fpm php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath composer git unzip
```

## 2. Aurora PostgreSQL設定
```bash
# .envファイルでAurora PostgreSQL接続設定
# DB_CONNECTION=pgsql
# DB_HOST=your-aurora-cluster-endpoint.cluster-xxxxx.region.rds.amazonaws.com
# DB_PORT=5432
# DB_DATABASE=evox_production
# DB_USERNAME=evox_user
# DB_PASSWORD=your-secure-password

# 接続テスト
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.region.rds.amazonaws.com -U evox_user -d evox_production
```

## 3. アプリケーションディレクトリ準備
```bash
sudo mkdir -p /var/www/evox
sudo chown -R $USER:$USER /var/www/evox
sudo chmod -R 755 /var/www/evox
```

## 4. Apache設定
```bash
sudo a2enmod proxy proxy_http rewrite expires
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

## 5. アプリケーションのデプロイ
```bash
cd /var/www/evox
git clone your-repository-url .
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6. 権限設定
```bash
sudo chown -R www-data:www-data /var/www/evox/storage
sudo chown -R www-data:www-data /var/www/evox/bootstrap/cache
sudo chmod -R 775 /var/www/evox/storage
sudo chmod -R 775 /var/www/evox/bootstrap/cache
```

## 7. サービス起動
```bash
sudo systemctl start apache2
sudo systemctl enable apache2
```

## 最小セットアップ（開発環境用）
```bash
sudo apt-get update
sudo apt-get install -y apache2 php8.3 php8.3-fpm php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath
```
