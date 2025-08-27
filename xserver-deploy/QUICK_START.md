# Xserver クイックスタートガイド

## 🚀 5分でデプロイ完了！

### 1. Xserverでの準備（2分）
1. **データベース作成**
   - Xserver管理画面 → MySQL管理 → 新しいデータベース作成
   - データベース名、ユーザー名、パスワードをメモ
   - 文字エンコーディングは「UTF-8(utf8mb4)」を選択

2. **ドメイン設定**
   - サブドメインまたは独自ドメインを設定
   - SSL証明書を有効化

### 2. ファイルアップロード（2分）
1. **FTP接続**
   - XserverのFTP情報で接続
   - `public_html`フォルダにアクセス

2. **ファイル転送**
   - `xserver-deploy/upload/` 内のファイルを全てアップロード
   - フォルダ構造を維持

### 3. 設定完了（1分）
1. **環境変数設定**
   ```bash
   # backend/.env を編集
   DB_CONNECTION=mysql
   DB_HOST=your-db-host
   DB_PORT=3306
   DB_DATABASE=your-db-name
   DB_USERNAME=your-db-user
   DB_PASSWORD=your-db-password
   
   # Twilio設定（必要に応じて）
   TWILIO_SID=your-twilio-sid
   TWILIO_TOKEN=your-twilio-token
   TWILIO_PHONE=your-twilio-phone
   ```

2. **アプリケーションキー生成**
   ```bash
   php artisan key:generate
   ```

3. **データベース設定**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **権限設定**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

### 4. 動作確認
- ブラウザで `https://your-domain.com` にアクセス
- フロントエンドが表示されることを確認
- APIが正常に動作することを確認

## 📁 アップロードするファイル
```
xserver-deploy/upload/
├── .htaccess          # URLルーティング設定
├── backend/           # Laravelバックエンド
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── artisan
│   ├── composer.json
│   └── .env
└── frontend/          # Vue.jsフロントエンド（ビルド済み）
    └── dist/
        ├── index.html
        └── assets/
```

## ⚠️ 注意事項
- ファイルアップロード時はフォルダ構造を維持
- `.env`ファイルの設定値を必ず実際の値に変更
- 権限設定を忘れずに実行
- エラーが発生した場合はログファイルを確認

## 🔧 トラブルシューティング
- **500エラー**: 権限設定を確認
- **データベース接続エラー**: .env設定を確認
- **API接続エラー**: .htaccessファイルを確認
