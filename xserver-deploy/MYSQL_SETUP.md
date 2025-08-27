# Xserver MySQL設定ガイド

## 概要
XserverではPostgreSQLが利用できないため、MySQL 5.7.xまたはMariaDB 10.5.xを使用します。

## Xserverのデータベース仕様
- **対応データベース**: MySQL 5.7.x、MariaDB 10.5.x
- **文字エンコーディング**: UTF-8(utf8mb4)、UTF-8(utf8)、EUC-JP、Shift-JIS、Binary
- **最大容量**: 5GB
- **接続方式**: MySQLデータベース1個につき1つの接続

## 設定手順

### 1. XserverでMySQLデータベースを作成
1. **Xserver管理画面にログイン**
   - [XServer管理画面](https://secure.xserver.ne.jp/xapanel/login/xserver/server/)にアクセス

2. **MySQLデータベース作成**
   - 「MySQL管理」から新しいデータベースを作成
   - データベース名、ユーザー名、パスワードを設定
   - 文字エンコーディングは「UTF-8(utf8mb4)」を推奨

### 2. 環境変数設定
```env
# backend/.env を編集
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

### 3. データベース設定
```bash
# マイグレーション実行
php artisan migrate

# シーダー実行
php artisan db:seed
```

## 注意事項

### 文字エンコーディング
- **推奨**: UTF-8(utf8mb4)
- **理由**: 絵文字や特殊文字に対応

### 接続制限
- 1つのデータベースにつき1つの接続のみ
- 同時接続数に制限がある場合があります

### 容量制限
- 最大5GBまで利用可能
- 定期的に容量を確認してください

## トラブルシューティング

### よくある問題
1. **接続エラー**: ホスト名、ポート番号を確認
2. **認証エラー**: ユーザー名、パスワードを確認
3. **文字化け**: 文字エンコーディング設定を確認

### 確認コマンド
```bash
# データベース接続確認
php artisan tinker
DB::connection()->getPdo();

# マイグレーション状態確認
php artisan migrate:status
```

## 参考リンク
- [Xserver データベース仕様](https://www.xserver.ne.jp/manual/man_db_spec.php)
