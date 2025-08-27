# Railway 環境変数設定ガイド

## RailwayプロジェクトURL
https://railway.com/project/6e3503d7-6fa4-40ca-943b-a317fa43c05d

## 設定手順

### 1. Railwayダッシュボードにアクセス
- https://railway.com/project/6e3503d7-6fa4-40ca-943b-a317fa43c05d にアクセス
- GitHubアカウントでログイン

### 2. データベース設定
- PostgreSQLサービスを選択
- "Connect" タブで接続情報を確認
- 以下の環境変数を設定：

```
DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}
```

### 3. アプリケーション環境変数
- メインサービスを選択
- "Variables" タブで以下を設定：

```
APP_NAME=evoX
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-railway-app.railway.app

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

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

# Twilio設定（必要に応じて）
TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_VERIFY_SERVICE_SID=your_verify_service_sid
TWILIO_PHONE_NUMBER=your_twilio_phone_number
```

### 4. GitHubリポジトリ接続
- "Settings" タブで "Connect Repository" をクリック
- GitHubリポジトリ `tsubu/evox-demo` を選択
- ブランチ `main` を選択

### 5. デプロイ設定
- "Settings" タブで以下を設定：
  - Root Directory: `backend`
  - Build Command: `composer install --no-dev --optimize-autoloader`
  - Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

### 6. デプロイ実行
- "Deployments" タブで "Deploy Now" をクリック
- デプロイ完了後、以下のコマンドを実行：

```bash
# データベースマイグレーション
php artisan migrate --force

# データベースシード
php artisan db:seed --force

# キャッシュクリア
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Vercel設定更新
デプロイ完了後、RailwayのURLを取得してVercelの設定を更新：

1. RailwayアプリのURLを確認（例：https://evox-production-xxxx.up.railway.app）
2. Vercelダッシュボードでプロジェクト設定を開く
3. `vercel.json`の`destination`を更新：

```json
{
  "rewrites": [
    {
      "source": "/api/(.*)",
      "destination": "https://evox-production-xxxx.up.railway.app/api/$1"
    }
  ]
}
```

4. 再デプロイを実行

## 注意事項
- APP_KEYは `php artisan key:generate` で生成
- 本番環境ではAPP_DEBUG=falseに設定
- データベース接続情報はRailwayが自動設定
- 環境変数設定後は再デプロイが必要
