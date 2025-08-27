#!/bin/bash

# EvoX ワンクリックAWSデプロイスクリプト
# 使用方法: ./one-click-deploy.sh

set -e

# 色付き出力用の関数
print_info() {
    echo -e "\033[1;34mℹ️  $1\033[0m"
}

print_success() {
    echo -e "\033[1;32m✅ $1\033[0m"
}

print_warning() {
    echo -e "\033[1;33m⚠️  $1\033[0m"
}

print_error() {
    echo -e "\033[1;31m❌ $1\033[0m"
}

print_header() {
    echo -e "\033[1;36m"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                    EvoX AWS デプロイ                        ║"
    echo "║              Lightsail + RDS (Aurora PostgreSQL)            ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo -e "\033[0m"
}

# スクリプトディレクトリ
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROD_DIR="$(dirname "$SCRIPT_DIR")"

print_header

print_info "=== デプロイ前チェック ==="

# 1. 設定ファイルの確認
CONFIG_FILE="$SCRIPT_DIR/aws-config.sh"
if [ ! -f "$CONFIG_FILE" ]; then
    print_error "設定ファイルが見つかりません: $CONFIG_FILE"
    print_info ""
    print_info "設定ファイルを作成してください:"
    print_info "1. cp aws-config.sh.example aws-config.sh"
    print_info "2. aws-config.shを編集して実際の値を設定"
    print_info "3. 再度このスクリプトを実行"
    exit 1
fi

# 設定ファイルを読み込み
source "$CONFIG_FILE"

# 2. 必要な値の確認
print_info "設定値を確認中..."

REQUIRED_VARS=(
    "AWS_REGION"
    "AWS_ACCOUNT_ID"
    "LIGHTSAIL_INSTANCE_NAME"
    "RDS_CLUSTER_IDENTIFIER"
    "RDS_ENDPOINT"
    "DOMAIN_NAME"
    "TWILIO_ACCOUNT_SID"
    "TWILIO_AUTH_TOKEN"
    "TWILIO_FROM_NUMBER"
)

for var in "${REQUIRED_VARS[@]}"; do
    if [ -z "${!var}" ] || [ "${!var}" = "your-*" ]; then
        print_error "設定値が不足しています: $var"
        print_info "aws-config.shファイルで $var を設定してください"
        exit 1
    fi
done

print_success "設定値の確認が完了しました"

# 3. 本番環境ファイルの準備確認
if [ ! -d "$PROD_DIR/backend" ] || [ ! -d "$PROD_DIR/frontend" ]; then
    print_warning "本番環境ファイルが準備されていません"
    print_info "本番環境ファイルを準備します..."
    
    if [ -f "$PROD_DIR/prepare-production.sh" ]; then
        chmod +x "$PROD_DIR/prepare-production.sh"
        "$PROD_DIR/prepare-production.sh"
    else
        print_error "prepare-production.shが見つかりません"
        exit 1
    fi
fi

# 4. デプロイ確認
print_info ""
print_info "=== デプロイ情報 ==="
print_info "リージョン: $AWS_REGION"
print_info "Lightsailインスタンス: $LIGHTSAIL_INSTANCE_NAME"
print_info "RDSクラスター: $RDS_CLUSTER_IDENTIFIER"
print_info "ドメイン: $DOMAIN_NAME"
print_info ""

read -p "デプロイを開始しますか？ (y/N): " confirm

if [[ ! $confirm =~ ^[Yy]$ ]]; then
    print_info "デプロイをキャンセルしました"
    exit 0
fi

# 5. デプロイ実行
print_info ""
print_info "=== デプロイ開始 ==="

# デプロイスクリプトを実行
if [ -f "$SCRIPT_DIR/deploy-to-lightsail.sh" ]; then
    chmod +x "$SCRIPT_DIR/deploy-to-lightsail.sh"
    "$SCRIPT_DIR/deploy-to-lightsail.sh"
else
    print_error "deploy-to-lightsail.shが見つかりません"
    exit 1
fi

print_info ""
print_success "=== デプロイ完了 ==="
print_info ""
print_info "🎉 EvoXアプリケーションが正常にデプロイされました！"
print_info ""
print_info "📱 アクセス情報:"
print_info "   URL: https://$DOMAIN_NAME"
print_info "   IP: $(aws lightsail get-instance --instance-name $LIGHTSAIL_INSTANCE_NAME --query 'instance.publicIpAddress' --output text)"
print_info ""
print_info "🔧 管理情報:"
print_info "   Lightsailコンソール: https://console.aws.amazon.com/lightsail/home?region=$AWS_REGION"
print_info "   RDSコンソール: https://console.aws.amazon.com/rds/home?region=$AWS_REGION"
print_info ""
print_info "📋 次のステップ:"
print_info "   1. アプリケーションの動作確認"
print_info "   2. データベースのバックアップ設定"
print_info "   3. 監視とログの設定"
print_info "   4. セキュリティの確認"
print_info ""
print_info "🚀 アプリケーションが正常に動作していることを確認してください！"
