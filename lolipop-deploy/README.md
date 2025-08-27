# EvoX ロリポップサーバー デプロイガイド

## 概要
このディレクトリにはロリポップサーバーへのデプロイ用ファイルが含まれています。

## ロリポップサーバーの特徴
- **PHP 8.1対応**
- **MySQL 8.0対応**
- **簡単な設定**
- **FTP接続が標準**
- **コストが安い**

## ファイル構成
```
lolipop-deploy/
├── backend/                 # Laravelバックエンド
├── frontend/               # Vue.jsフロントエンド（ビルド済み）
├── upload/                 # アップロード用ファイル
├── scripts/               # デプロイスクリプト
└── README.md
```

## 🚀 デプロイ手順

### 1. ロリポップサーバーでの準備
1. **データベース作成**
   - ロリポップ管理画面でMySQLデータベースを作成
   - データベース名、ユーザー名、パスワードをメモ

2. **ドメイン設定**
   - サブドメインまたは独自ドメインを設定
   - SSL証明書を有効化

### 2. ファイルアップロード
1. **FTP接続**
   - ロリポップのFTP情報で接続
   - `public_html`フォルダにアクセス

2. **ファイル転送**
   - `upload/`フォルダ内のファイルを全てアップロード
   - フォルダ構造を維持

### 3. 設定ファイルの編集
1. **バックエンド設定**
   ```bash
   # backend/.env を編集
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your-db-name
   DB_USERNAME=your-db-user
   DB_PASSWORD=your-db-password
   
   # Twilio設定
   TWILIO_SID=your-twilio-sid
   TWILIO_TOKEN=your-twilio-token
   TWILIO_PHONE=your-twilio-phone
   ```

### 4. データベース設定
1. **マイグレーション実行**
   ```bash
   cd backend
   php artisan migrate
   ```

2. **シーダー実行**
   ```bash
   php artisan db:seed
   ```

### 5. 権限設定
```bash
# ストレージ権限
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# ログ権限
chmod -R 755 storage/logs/
```

## 📋 必要な設定

### 環境変数
- データベース接続情報
- Twilio認証情報
- アプリケーションキー
- セッション設定

### ファイル権限
- storage/: 755
- bootstrap/cache/: 755
- storage/logs/: 755

### URL設定
- フロントエンド: https://your-domain.com
- バックエンドAPI: https://your-domain.com/api

## 🔧 トラブルシューティング

### よくある問題
1. **500エラー**: 権限設定を確認
2. **データベース接続エラー**: .env設定を確認
3. **API接続エラー**: CORS設定を確認

### ログ確認
```bash
# Laravelログ
tail -f storage/logs/laravel.log

# エラーログ
tail -f storage/logs/error.log
```

## 📞 サポート
問題が発生した場合は、以下を確認してください：
1. ロリポップのエラーログ
2. Laravelのログファイル
3. ブラウザの開発者ツール
