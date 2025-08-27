# 管理画面ログインテスト手順ガイド

## 概要
このガイドでは、実際のブラウザ環境で管理画面のログイン機能をテストする手順を説明します。

## 前提条件
- Laravelサーバーが起動している（`php artisan serve --host=0.0.0.0 --port=8000`）
- PostgreSQLデータベースが起動している
- 管理者アカウントが作成されている

## テスト手順

### 1. ログインページにアクセス
ブラウザで以下のURLにアクセス：
```
http://localhost:8000/admin-ai8edq64p2i5
```

### 2. 認証情報を入力
- **電話番号**: `8090330374`（+81は自動で付与される）
- **パスワード**: `admin123`

### 3. 認証コード発行
「認証コードを発行」ボタンをクリック

### 4. OTPコードを取得
データベースからOTPコードを取得：
```sql
SELECT adminopt_code FROM otp_codes_admin 
WHERE adminopt_temp_id LIKE 'test_%' 
ORDER BY adminopt_created_at DESC LIMIT 1;
```

### 5. OTPコードを入力
取得した6桁のOTPコードを入力フィールドに入力

### 6. 認証実行
「認証」ボタンをクリック

### 7. ダッシュボードアクセス確認
- ダッシュボード（`http://localhost:8000/admin/dashboard`）にリダイレクトされるか確認
- 「EvoX 管理画面」のタイトルが表示されるか確認

## デバッグ情報の確認

### ブラウザの開発者ツールで確認
1. **Console** タブでJavaScriptのログを確認
2. **Application** タブでLocalStorageとCookiesの状態を確認
3. **Network** タブでリクエストヘッダーを確認

### 期待されるログ
```
Token saved: {token: "...", localStorage: "...", cookie: "..."}
localStorage admin_token: present
admin_token in cookies: present
```

### Laravelログの確認
```bash
tail -f storage/logs/laravel.log
```

## 問題の特定と解決

### 問題1: ログインページにリダイレクトされる
**原因**: トークンが正しく保存されていない、または認証ミドルウェアがトークンを認識していない

**解決策**:
1. JavaScriptのコンソールログを確認
2. Cookieの設定を確認
3. 認証ミドルウェアのログを確認

### 問題2: OTP認証でエラーが発生
**原因**: OTPコードが間違っている、または期限切れ

**解決策**:
1. データベースから最新のOTPコードを再取得
2. 新しい認証コードを発行

### 問題3: JavaScriptエラーが発生
**原因**: ブラウザの互換性問題やJavaScriptの構文エラー

**解決策**:
1. ブラウザのコンソールでエラーメッセージを確認
2. 必要に応じてJavaScriptコードを修正

## 自動テストの実行

### curlベースのテスト
```bash
php simple_auto_test.php
```

### Selenium WebDriverテスト（ChromeDriverが必要）
```bash
php auto_admin_test.php
```

## 成功の確認

以下の条件がすべて満たされれば、テストは成功です：

1. ✅ ログインページに正常にアクセス
2. ✅ OTP認証コードが正常に送信される
3. ✅ OTP認証が成功し、トークンが生成される
4. ✅ ダッシュボードに正常にアクセスできる
5. ✅ ダッシュボードの内容が正しく表示される
6. ✅ トークンがlocalStorageとCookieに正しく保存される

## トラブルシューティング

### ChromeDriverの問題
```bash
# ChromeDriverを再インストール
brew uninstall chromedriver
brew install chromedriver

# 権限を確認
ls -la /opt/homebrew/bin/chromedriver
```

### データベース接続の問題
```bash
# PostgreSQLの接続確認
psql -h localhost -p 5432 -U tsuburayatoshifumi -d evox
```

### Laravelサーバーの問題
```bash
# サーバーを再起動
php artisan serve --host=0.0.0.0 --port=8000
```
