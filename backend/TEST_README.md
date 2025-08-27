# EvoX 管理画面 自動テスト

このディレクトリには、EvoX管理画面の自動テストが含まれています。

## テスト構成

### 1. 認証テスト (`AdminAuthenticationTest.php`)
- 管理画面ログインページへのアクセス
- OTP認証の要求
- OTP認証の検証
- トークンベース認証
- ログアウト機能
- OTPコードの有効期限
- OTPコードの一回性

### 2. 機能テスト (`AdminManagementTest.php`)
- ユーザー管理機能
- ニュース管理機能
- QRコード管理機能
- 統計情報表示
- 認証保護されたルートのアクセス制御

## テスト実行方法

### 1. 全テストを実行
```bash
./run_tests.sh
```

### 2. 特定のテストファイルを実行
```bash
# 認証テストのみ実行
php artisan test tests/Feature/AdminAuthenticationTest.php --verbose

# 機能テストのみ実行
php artisan test tests/Feature/AdminManagementTest.php --verbose
```

### 3. 全テストを実行
```bash
php artisan test --verbose
```

### 4. テストカバレッジを確認
```bash
php artisan test --coverage
```

## テスト環境設定

### データベース
- テスト用にSQLiteインメモリデータベースを使用
- 各テスト実行前にデータベースがリセットされる

### 環境変数
- `APP_ENV=testing`
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=:memory:`
- `SESSION_DRIVER=array`
- `ADMIN_URL_HASH=ai8edq64p2i5`

## テストデータ

### 管理者ユーザー
- 電話番号: `+818090330374`
- パスワード: `admin123`
- 名前: `Test Administrator`

### テストユーザー
- 電話番号: `+818090330375`
- パスワード: `password`
- 名前: `Test User`
- メール: `test@example.com`

## テストの追加方法

### 1. 新しいテストメソッドを追加
```php
/** @test */
public function test_new_feature()
{
    // テストコード
}
```

### 2. 新しいテストクラスを作成
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class NewFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function test_feature()
    {
        // テストコード
    }
}
```

## トラブルシューティング

### 1. テストが失敗する場合
- データベースのマイグレーションが最新か確認
- テスト用の環境変数が正しく設定されているか確認
- 依存関係がインストールされているか確認

### 2. テストが遅い場合
- 不要なテストデータを削除
- テストの並列実行を検討
- データベースのインデックスを最適化

### 3. メモリ不足の場合
- テストを分割して実行
- データベースのクリーンアップを確認
- PHPのメモリ制限を増加

## CI/CD統合

### GitHub Actions
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php artisan test
```

### ローカル開発
```bash
# 開発中にテストを実行
php artisan test --filter=AdminAuthenticationTest

# 特定のテストメソッドを実行
php artisan test --filter=test_admin_can_login
```

## 注意事項

1. **テストデータの分離**: 各テストは独立して実行される必要があります
2. **データベースのクリーンアップ**: テスト後にデータベースがクリーンアップされることを確認
3. **認証トークン**: テストでは有効な認証トークンを使用
4. **外部依存**: SMS送信などの外部サービスはモック化

## 関連ファイル

- `tests/Feature/AdminAuthenticationTest.php` - 認証テスト
- `tests/Feature/AdminManagementTest.php` - 機能テスト
- `database/factories/UserAdminFactory.php` - 管理者ファクトリー
- `database/seeders/TestSeeder.php` - テスト用シーダー
- `phpunit.xml` - PHPUnit設定
- `run_tests.sh` - テスト実行スクリプト
