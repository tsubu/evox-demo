# AWS Lightsail + RDS Aurora デプロイメントチェックリスト

## ✅ 現在対応済み
- [x] Vue 3 + Laravel 11 アーキテクチャ
- [x] PostgreSQL対応データベース設計
- [x] RESTful API完全実装
- [x] Sanctum認証システム
- [x] 管理者権限制御
- [x] QRコード読み取り機能
- [x] ポイントシステム
- [x] ニュース管理機能

## 🔧 AWS移植に必要な作業

### 1. 環境設定
- [ ] AWS RDS Aurora PostgreSQL 16接続設定
- [ ] 環境変数設定（.env.production）
- [ ] SSL証明書設定
- [ ] セキュリティグループ設定

### 2. AWSサービス統合
- [ ] SQS（キュー）設定
- [ ] SES（メール）設定
- [ ] CloudWatch（ログ）設定
- [ ] ElastiCache Redis（キャッシュ）設定

### 3. デプロイ設定
- [ ] Lightsail Apache設定
- [ ] PHP-FPM設定
- [ ] 静的ファイル配信設定
- [ ] ヘルスチェック設定

### 4. セキュリティ強化
- [ ] WAF設定
- [ ] レート制限強化
- [ ] CORS設定
- [ ] セキュリティヘッダー設定

### 5. 監視・ログ
- [ ] CloudWatchダッシュボード
- [ ] アラート設定
- [ ] ログ集約設定
- [ ] パフォーマンス監視

## 🚀 デプロイ手順

### Phase 1: 準備
```bash
# 1. データベース接続確認
php artisan migrate --force

# 2. フロントエンドビルド
cd frontend
npm run build
```

### Phase 2: AWS設定
```bash
# 1. RDS接続テスト
php artisan tinker
DB::connection()->getPdo();

# 2. 環境変数設定
cp .env.example .env.production
# AWS設定を反映
```

### Phase 3: デプロイ
```bash
# 1. Laravel配置
# /var/www/html/api/ にLaravel配置

# 2. フロントエンド配置
# /var/www/html/ にVue静的ファイル配置

# 3. Apache設定
# RewriteRule設定でSPAフォールバック
```

## 📊 パフォーマンス目標
- LCP < 2.5s（PC）/ < 3.0s（SP）
- Lighthouse 80+
- API応答時間 < 500ms

## 🔒 セキュリティ要件
- HTTPS必須
- レート制限適用
- 管理者権限制御
- SQLインジェクション対策
- XSS対策

## 💰 コスト最適化
- Aurora Serverless v2検討
- CloudFront CDN活用
- S3静的ホスティング検討
- コスト監視設定
