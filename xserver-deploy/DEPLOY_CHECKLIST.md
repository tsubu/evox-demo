# Xserver デプロイチェックリスト

## 事前準備
- [ ] XserverでMySQLデータベースを作成
- [ ] ドメイン設定（SSL有効化）
- [ ] FTP接続情報を確認

## ファイルアップロード
- [ ] upload/フォルダ内のファイルを全てアップロード
- [ ] フォルダ構造を維持
- [ ] .htaccessファイルが正しく配置されている

## 設定ファイル編集
- [ ] backend/.env のデータベース接続情報を設定
- [ ] backend/.env のTwilio認証情報を設定
- [ ] アプリケーションキーを生成（php artisan key:generate）

## データベース設定
- [ ] マイグレーション実行（php artisan migrate）
- [ ] シーダー実行（php artisan db:seed）

## 権限設定
- [ ] set-permissions.sh を実行
- [ ] ストレージフォルダの権限確認

## 動作確認
- [ ] フロントエンドが表示される
- [ ] APIが正常に動作する
- [ ] データベース接続が正常
- [ ] ログファイルが作成される

## トラブルシューティング
- [ ] エラーログを確認
- [ ] 権限設定を再確認
- [ ] 設定ファイルの構文エラーを確認

## 環境変数設定例

### backend/.env
```env
APP_NAME=EvoX
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
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

# Twilio設定
TWILIO_SID=your-twilio-sid
TWILIO_TOKEN=your-twilio-token
TWILIO_PHONE=your-twilio-phone
```
