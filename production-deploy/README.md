# EvoX 本番環境デプロイ

## 概要
このディレクトリには本番環境用のファイルとAWS Lightsail + RDS（Aurora PostgreSQL）へのワンクリックデプロイ機能が含まれています。

## ファイル構成
```
production-deploy/
├── backend/                 # Laravelバックエンド
├── frontend/               # Vue.jsフロントエンド
├── config/                 # 設定ファイル
├── aws-deploy/            # AWSデプロイ用スクリプト
│   ├── aws-config.sh.example    # AWS設定ファイル例
│   ├── deploy-to-aws.sh         # ECS/ECR用デプロイスクリプト
│   ├── deploy-to-lightsail.sh   # Lightsail + RDS用デプロイスクリプト
│   └── one-click-deploy.sh      # ワンクリックデプロイメインスクリプト
├── scripts/               # ユーティリティスクリプト
├── docker-compose.production.yml
├── deploy.sh
├── prepare-production.sh
└── README.md
```

## 🚀 ワンクリックデプロイ（推奨）

### 1. 設定ファイルの準備
```bash
cd aws-deploy
cp aws-config.sh.example aws-config.sh
```

### 2. 設定値の編集
`aws-config.sh`ファイルを編集して実際の値を設定：
- AWS認証情報
- Lightsailインスタンス設定
- RDS（Aurora PostgreSQL）設定
- ドメイン名
- Twilio認証情報

### 3. ワンクリックデプロイ実行
```bash
./one-click-deploy.sh
```

このスクリプトは以下を自動実行します：
- ✅ 本番環境ファイルの準備
- ✅ AWS認証情報の確認
- ✅ Lightsailインスタンスの作成
- ✅ RDS Auroraクラスターの作成
- ✅ アプリケーションのデプロイ
- ✅ SSL証明書の設定
- ✅ Nginx設定の構成

## 🔧 手動デプロイ

### 1. 本番環境ファイルの準備
```bash
./prepare-production.sh
```

### 2. 環境変数の設定
```bash
# バックエンド
cp backend/.env.production backend/.env
# フロントエンド
cp frontend/.env.production frontend/.env
```

### 3. 設定値の更新
各.envファイルの以下の値を実際の値に更新：
- データベース接続情報
- Twilio認証情報
- AWS認証情報
- ドメイン名

### 4. Docker Composeで起動（ローカルテスト用）
```bash
docker-compose -f docker-compose.production.yml up -d
```

### 5. 手動デプロイ
```bash
./deploy.sh
```

## 📋 必要なAWSリソース

### Lightsail
- Ubuntu 22.04 インスタンス
- 2GB RAM, 1 vCPU（推奨）
- SSHキーペア

### RDS (Aurora PostgreSQL)
- Aurora PostgreSQL 15.4
- db.t3.micro（開発用）
- セキュリティグループ設定

### その他
- ドメイン名（Route 53推奨）
- SSL証明書（Let's Encrypt自動取得）
- セキュリティグループ

## 🔐 セキュリティ設定

### ファイアウォール
- SSH (22)
- HTTP (80)
- HTTPS (443)

### データベース
- プライベートサブネット
- セキュリティグループでアクセス制限

### SSL/TLS
- Let's Encrypt証明書を自動取得
- HTTPS強制リダイレクト

## 📊 監視とログ

### CloudWatch
- アプリケーションログ
- システムメトリクス
- アラート設定

### ログファイル
- Nginxアクセスログ
- Laravelアプリケーションログ
- システムログ

## 🔄 更新とメンテナンス

### アプリケーション更新
```bash
# 新しいバージョンをデプロイ
./aws-deploy/one-click-deploy.sh
```

### データベースバックアップ
- RDS自動バックアップを有効化
- 手動スナップショットの作成

### セキュリティ更新
- 定期的なシステムアップデート
- セキュリティパッチの適用

## 🆘 トラブルシューティング

### よくある問題

1. **SSH接続エラー**
   - SSHキーペアの権限確認
   - セキュリティグループの設定確認

2. **データベース接続エラー**
   - RDSエンドポイントの確認
   - セキュリティグループの設定確認

3. **SSL証明書エラー**
   - ドメイン名の設定確認
   - Certbotの再実行

### ログの確認
```bash
# Nginxログ
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# アプリケーションログ
sudo tail -f /var/www/evox/backend/storage/logs/laravel.log

# システムログ
sudo journalctl -u evox-backend -f
```

## 📞 サポート

問題が発生した場合は以下を確認してください：
1. AWSコンソールでのリソース状態
2. アプリケーションログ
3. システムログ
4. ネットワーク接続

## 🎯 次のステップ

デプロイ完了後：
1. アプリケーションの動作確認
2. データベースのバックアップ設定
3. 監視とアラートの設定
4. セキュリティの強化
5. パフォーマンスの最適化
