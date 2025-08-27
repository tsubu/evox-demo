# EvoX Teaser Site

EvoXのティザーサイトです。Vue.js + Laravelで構築された事前登録サイトです。

## プロジェクト構成

```
evox/
├── frontend/          # Vue.js フロントエンド
├── backend/           # Laravel バックエンド
├── images/            # 画像ファイル
├── siteimage/         # サイト用画像
├── 仕様書/            # 仕様書
└── README.md
```

## 技術スタック

### フロントエンド
- Vue 3 + Vite
- Vue Router
- Pinia (状態管理)
- Tailwind CSS
- Axios (HTTP通信)
- @zxing/library (QRコード読み取り)

### バックエンド
- Laravel 11
- Laravel Sanctum (認証)
- PostgreSQL (データベース)

## セットアップ

### 前提条件
- Node.js 18+
- PHP 8.3+
- Composer
- PostgreSQL

### フロントエンド

```bash
cd frontend
npm install
npm run dev
```

### バックエンド

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## 主な機能

### 公開機能
- ホームページ（Hero、カウントダウン、世界観）
- 事前登録（電話番号 + OTP認証）
- 登録者数表示
- ニュースティッカー

### 認証機能
- ログイン/ログアウト
- パスワードリセット
- セッション管理

### ユーザー機能
- マイページ
- QRコード読み取り
- ポイント管理
- イベント参加
- お知らせ閲覧
- GIFT交換

### 管理者機能
- ユーザー管理
- ニュース管理
- QRコード管理

## デザインカスタマイズ

### カラーテーマ
Tailwind CSSの設定でカスタマイズ可能です：

```javascript
// tailwind.config.js
theme: {
  extend: {
    colors: {
      'evox-black': '#000000',
      'evox-gray': '#1a1a1a',
      'evox-blue': '#0066cc',
      'evox-gold': '#ffd700',
    }
  }
}
```

### コンポーネント
各コンポーネントは独立しており、以下の方法でカスタマイズできます：

1. **CSSクラスの変更**: Tailwind CSSクラスを直接変更
2. **コンポーネントの修正**: Vue.jsコンポーネントファイルを編集
3. **スタイルの追加**: `src/assets/main.css`にカスタムスタイルを追加

### 画像の変更
- `images/`フォルダ内の画像を置き換え
- `siteimage/`フォルダ内のデザイン画像を置き換え

## 開発ガイド

### 新しいページの追加
1. `frontend/src/pages/`に新しいVueファイルを作成
2. `frontend/src/router/index.js`にルートを追加
3. 必要に応じてコンポーネントを作成

### 新しいAPIエンドポイントの追加
1. `backend/app/Http/Controllers/`にコントローラーを作成
2. `backend/routes/api.php`にルートを追加
3. 必要に応じてマイグレーションを作成

### データベースの変更
1. 新しいマイグレーションファイルを作成
2. モデルファイルを更新
3. `php artisan migrate`を実行

## デプロイ

### フロントエンド
```bash
cd frontend
npm run build
```

### バックエンド
```bash
cd backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## ライセンス

MIT License

## お問い合わせ

EvoX開発チーム
Email: dev@evox.com
