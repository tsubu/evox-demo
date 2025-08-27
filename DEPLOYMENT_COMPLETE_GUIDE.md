# 完全代行デプロイ完了ガイド

## 🎉 現在の状況

### ✅ 完了済み
1. **GitHubリポジトリ**: https://github.com/tsubu/evox-demo.git
2. **Vercel（フロントエンド）**: https://evox-7ylvfduth-tsubus-projects-f3a579b4.vercel.app
3. **Railwayプロジェクト**: https://railway.com/project/6e3503d7-6fa4-40ca-943b-a317fa43c05d
4. **PostgreSQLデータベース**: Railwayに追加済み
5. **APP_KEY生成**: `base64:+A96Ohnla09XfP0MpGHeQwzi1LmIOJgKc6JxgROuwW8=`

### 🔄 残りの作業
Railwayのバックエンド設定のみ

## 🚀 Railway設定手順

### 1. Railwayダッシュボードにアクセス
https://railway.com/project/6e3503d7-6fa4-40ca-943b-a317fa43c05d

### 2. GitHubリポジトリ接続
1. "Settings" タブをクリック
2. "Connect Repository" をクリック
3. GitHubリポジトリ `tsubu/evox-demo` を選択
4. ブランチ `main` を選択

### 3. サービス設定
1. メインサービス（GitHubリポジトリ）を選択
2. "Settings" タブで以下を設定：
   - **Root Directory**: `backend`
   - **Build Command**: `composer install --no-dev --optimize-autoloader`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

### 4. 環境変数設定
1. メインサービスの "Variables" タブをクリック
2. 以下の環境変数を追加：

```
APP_NAME=evoX
APP_ENV=production
APP_KEY=base64:+A96Ohnla09XfP0MpGHeQwzi1LmIOJgKc6JxgROuwW8=
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

# データベース設定（PostgreSQL）
DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

# Twilio設定（必要に応じて）
TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_VERIFY_SERVICE_SID=your_verify_service_sid
TWILIO_PHONE_NUMBER=your_twilio_phone_number
```

### 5. デプロイ実行
1. "Deployments" タブをクリック
2. "Deploy Now" をクリック
3. デプロイ完了を待つ

### 6. データベース初期化
デプロイ完了後、Railwayのターミナルで以下を実行：

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

### 7. Railway URL取得
デプロイ完了後、RailwayアプリのURLを確認（例：https://evox-production-xxxx.up.railway.app）

### 8. Vercel設定更新
1. Vercelダッシュボードにアクセス
2. プロジェクト設定を開く
3. `vercel.json`の`destination`をRailwayのURLに更新
4. 再デプロイを実行

## 🔗 最終的なURL構成

- **フロントエンド**: https://evox-7ylvfduth-tsubus-projects-f3a579b4.vercel.app
- **バックエンドAPI**: https://your-railway-app.railway.app/api/
- **管理画面**: https://evox-7ylvfduth-tsubus-projects-f3a579b4.vercel.app/admin

## 📋 確認事項

1. **フロントエンド**: Vercelで正常に動作中
2. **バックエンド**: Railwayでデプロイ後、APIエンドポイントが正常に応答するか確認
3. **データベース**: PostgreSQLでマイグレーションとシードが正常に実行されるか確認
4. **API連携**: フロントエンドからバックエンドAPIへのリクエストが正常に動作するか確認

## 🆘 トラブルシューティング

### Railwayデプロイエラー
- 環境変数が正しく設定されているか確認
- APP_KEYが正しく設定されているか確認
- データベース接続情報が正しいか確認

### API接続エラー
- CORS設定が正しいか確認
- Vercelのrewrite設定が正しいか確認
- RailwayのURLが正しく設定されているか確認

### データベースエラー
- PostgreSQL接続情報が正しいか確認
- マイグレーションが正常に実行されているか確認
- シードデータが正常に挿入されているか確認
