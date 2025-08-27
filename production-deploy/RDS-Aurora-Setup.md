# RDS Aurora PostgreSQL セットアップガイド

## 概要
EvoXアプリケーション用のRDS Aurora PostgreSQLクラスターのセットアップ手順です。

## 🗄️ RDS Auroraクラスター作成

### AWSコンソールでの作成手順

1. **AWS RDSコンソールにアクセス**
   - https://console.aws.amazon.com/rds/

2. **データベースの作成**
   - 「データベースの作成」をクリック
   - 「標準作成」を選択

3. **エンジンタイプの選択**
   - **エンジンタイプ**: Aurora (PostgreSQL 互換)
   - **バージョン**: Aurora PostgreSQL 15.4 (推奨)

4. **テンプレートの選択**
   - **本番環境**: 本番環境用
   - **開発/テスト**: 開発・テスト用

5. **設定**
   - **DB クラスター識別子**: `evox-aurora-cluster`
   - **マスターユーザー名**: `evox_admin`
   - **マスターパスワード**: 強力なパスワードを設定

6. **インスタンス設定**
   - **DB インスタンスクラス**: `db.t3.micro` (開発用) / `db.r6g.large` (本番用)
   - **マルチAZ配置**: 本番環境では有効化推奨

7. **ストレージ**
   - **ストレージタイプ**: Aurora 標準
   - **割り当て済みストレージ**: 20 GB (最小)

8. **接続**
   - **VPC**: デフォルトVPC
   - **サブネットグループ**: デフォルト
   - **パブリックアクセス**: はい (Lightsailからの接続用)
   - **VPCセキュリティグループ**: 新規作成
   - **可用性ゾーン**: 自動選択

9. **データベース認証**
   - **パスワード認証**: パスワード認証

10. **追加設定**
    - **初期データベース名**: `evox_production`
    - **バックアップ保持期間**: 7日間
    - **バックアップウィンドウ**: 03:00-04:00 UTC
    - **メンテナンスウィンドウ**: 日曜 04:00-05:00 UTC

11. **作成完了**
    - 「データベースの作成」をクリック

## 🔐 セキュリティグループ設定

### インバウンドルールの設定

```bash
# セキュリティグループIDを確認
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].VpcSecurityGroups[0].VpcSecurityGroupId' --output text
```

**インバウンドルールを追加:**
- **タイプ**: PostgreSQL
- **プロトコル**: TCP
- **ポート範囲**: 5432
- **ソース**: LightsailインスタンスのセキュリティグループID
- **説明**: Lightsail from EvoX

## 📊 データベースユーザー作成

### クラスターに接続

```bash
# エンドポイントを取得
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].Endpoint' --output text

# 接続
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com -U evox_admin -d evox_production
```

### アプリケーションユーザー作成

```sql
-- アプリケーション用ユーザー作成
CREATE USER evox_user WITH PASSWORD 'your-secure-password';

-- データベース作成（必要に応じて）
CREATE DATABASE evox_production;

-- 権限付与
GRANT ALL PRIVILEGES ON DATABASE evox_production TO evox_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO evox_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO evox_user;
GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA public TO evox_user;

-- デフォルト権限設定
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO evox_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO evox_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO evox_user;

-- 確認
\du
\q
```

## 🔧 接続情報の確認

### エンドポイント情報

```bash
# クラスターエンドポイント
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].Endpoint' --output text

# リーダーエンドポイント（マルチAZの場合）
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].ReaderEndpoint' --output text

# ポート
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].Port' --output text
```

### 接続テスト

```bash
# 接続テスト
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com -U evox_user -d evox_production -c "SELECT version();"

# 詳細接続情報
psql "host=your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com port=5432 dbname=evox_production user=evox_user password=your-secure-password"
```

## 📋 .envファイル設定

### バックエンド用.env設定

```env
DB_CONNECTION=pgsql
DB_HOST=your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com
DB_PORT=5432
DB_DATABASE=evox_production
DB_USERNAME=evox_user
DB_PASSWORD=your-secure-password
```

## 🔄 バックアップと復元

### 自動バックアップ

```bash
# バックアップ設定確認
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].{BackupRetentionPeriod:BackupRetentionPeriod,PreferredBackupWindow:PreferredBackupWindow}'
```

### 手動スナップショット

```bash
# スナップショット作成
aws rds create-db-cluster-snapshot \
    --db-cluster-identifier evox-aurora-cluster \
    --db-cluster-snapshot-identifier evox-backup-$(date +%Y%m%d-%H%M%S)

# スナップショット一覧
aws rds describe-db-cluster-snapshots --db-cluster-identifier evox-aurora-cluster
```

### データベース復元

```bash
# スナップショットから復元
aws rds restore-db-cluster-from-snapshot \
    --db-cluster-identifier evox-aurora-cluster-restored \
    --snapshot-identifier your-snapshot-identifier \
    --engine aurora-postgresql
```

## 📊 監視とメトリクス

### CloudWatchメトリクス

```bash
# 利用可能なメトリクス
aws cloudwatch list-metrics --namespace AWS/RDS --dimensions Name=DBClusterIdentifier,Value=evox-aurora-cluster

# CPU使用率
aws cloudwatch get-metric-statistics \
    --namespace AWS/RDS \
    --metric-name CPUUtilization \
    --dimensions Name=DBClusterIdentifier,Value=evox-aurora-cluster \
    --start-time $(date -u -d '1 hour ago' +%Y-%m-%dT%H:%M:%S) \
    --end-time $(date -u +%Y-%m-%dT%H:%M:%S) \
    --period 300 \
    --statistics Average
```

## 🔧 トラブルシューティング

### 接続エラー

```bash
# セキュリティグループ確認
aws ec2 describe-security-groups --group-ids sg-xxxxxxxxx

# ネットワークACL確認
aws ec2 describe-network-acls --filters "Name=association.subnet-id,Values=subnet-xxxxxxxxx"

# 接続テスト
telnet your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com 5432
```

### パフォーマンス問題

```bash
# スロークエリログ確認
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].DBClusterMembers[0].DBInstanceIdentifier' --output text

# パラメータグループ確認
aws rds describe-db-cluster-parameters --db-cluster-parameter-group-name default.aurora-postgresql15
```

## 💰 コスト最適化

### インスタンスサイズ調整

```bash
# 現在のインスタンスクラス確認
aws rds describe-db-clusters --db-cluster-identifier evox-aurora-cluster --query 'DBClusters[0].DBClusterMembers[0].DBInstanceClass' --output text

# インスタンスクラス変更
aws rds modify-db-instance \
    --db-instance-identifier your-instance-id \
    --db-instance-class db.t3.micro \
    --apply-immediately
```

### 自動停止（開発環境）

```bash
# 開発環境での自動停止設定
aws rds stop-db-cluster --db-cluster-identifier evox-aurora-cluster

# 自動停止のスケジュール設定（EventBridge使用）
aws events put-rule \
    --name "StopAuroraCluster" \
    --schedule-expression "cron(0 18 ? * MON-FRI *)" \
    --description "Stop Aurora cluster after business hours"
```

## 🎯 セットアップ完了確認

```bash
# 1. 接続テスト
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com -U evox_user -d evox_production -c "SELECT version();"

# 2. テーブル作成テスト
psql -h your-aurora-cluster-endpoint.cluster-xxxxx.ap-northeast-1.rds.amazonaws.com -U evox_user -d evox_production -c "CREATE TABLE test_table (id SERIAL PRIMARY KEY, name VARCHAR(100)); DROP TABLE test_table;"

# 3. アプリケーション接続テスト
curl -X POST http://localhost/api/test-db-connection
```

## 📞 サポート

問題が発生した場合は以下を確認してください：
1. セキュリティグループの設定
2. ネットワークACLの設定
3. サブネットの設定
4. パラメータグループの設定
5. CloudWatchログ
