#!/bin/bash

# AWSワンクリックデプロイスクリプト
# 使用方法: ./deploy-to-aws.sh

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

# 設定ファイルのパス
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROD_DIR="$(dirname "$SCRIPT_DIR")"
CONFIG_FILE="$SCRIPT_DIR/aws-config.sh"

print_info "=== AWSワンクリックデプロイ開始 ==="
print_info "スクリプトディレクトリ: $SCRIPT_DIR"
print_info "本番環境ディレクトリ: $PROD_DIR"

# 設定ファイルの存在確認
if [ ! -f "$CONFIG_FILE" ]; then
    print_error "設定ファイルが見つかりません: $CONFIG_FILE"
    print_info "aws-config.sh.exampleをコピーして設定してください"
    exit 1
fi

# 設定ファイルを読み込み
source "$CONFIG_FILE"

# 必要なツールの確認
check_requirements() {
    print_info "必要なツールを確認中..."
    
    # AWS CLI
    if ! command -v aws &> /dev/null; then
        print_error "AWS CLIがインストールされていません"
        print_info "https://aws.amazon.com/cli/ からインストールしてください"
        exit 1
    fi
    
    # Docker
    if ! command -v docker &> /dev/null; then
        print_error "Dockerがインストールされていません"
        print_info "https://docker.com/ からインストールしてください"
        exit 1
    fi
    
    # Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Composeがインストールされていません"
        print_info "https://docs.docker.com/compose/install/ からインストールしてください"
        exit 1
    fi
    
    print_success "必要なツールが確認されました"
}

# AWS認証情報の確認
check_aws_credentials() {
    print_info "AWS認証情報を確認中..."
    
    if ! aws sts get-caller-identity &> /dev/null; then
        print_error "AWS認証情報が設定されていません"
        print_info "以下のコマンドで設定してください:"
        print_info "aws configure"
        exit 1
    fi
    
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    print_success "AWS認証情報が確認されました (Account: $AWS_ACCOUNT_ID)"
}

# ECRリポジトリの作成
create_ecr_repositories() {
    print_info "ECRリポジトリを作成中..."
    
    # バックエンド用リポジトリ
    if ! aws ecr describe-repositories --repository-names evox-backend &> /dev/null; then
        aws ecr create-repository --repository-name evox-backend
        print_success "ECRリポジトリ 'evox-backend' を作成しました"
    else
        print_info "ECRリポジトリ 'evox-backend' は既に存在します"
    fi
    
    # フロントエンド用リポジトリ
    if ! aws ecr describe-repositories --repository-names evox-frontend &> /dev/null; then
        aws ecr create-repository --repository-name evox-frontend
        print_success "ECRリポジトリ 'evox-frontend' を作成しました"
    else
        print_info "ECRリポジトリ 'evox-frontend' は既に存在します"
    fi
}

# DockerイメージをビルドしてECRにプッシュ
build_and_push_images() {
    print_info "DockerイメージをビルドしてECRにプッシュ中..."
    
    # ECRログイン
    aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com
    
    # バックエンドイメージ
    print_info "バックエンドイメージをビルド中..."
    cd "$PROD_DIR/backend"
    docker build -f Dockerfile.production -t evox-backend .
    docker tag evox-backend:latest $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/evox-backend:latest
    docker push $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/evox-backend:latest
    print_success "バックエンドイメージをプッシュしました"
    
    # フロントエンドイメージ
    print_info "フロントエンドイメージをビルド中..."
    cd "$PROD_DIR/frontend"
    docker build -f Dockerfile.production -t evox-frontend .
    docker tag evox-frontend:latest $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/evox-frontend:latest
    docker push $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/evox-frontend:latest
    print_success "フロントエンドイメージをプッシュしました"
}

# ECSクラスターの作成
create_ecs_cluster() {
    print_info "ECSクラスターを作成中..."
    
    if ! aws ecs describe-clusters --clusters evox-cluster &> /dev/null; then
        aws ecs create-cluster --cluster-name evox-cluster
        print_success "ECSクラスター 'evox-cluster' を作成しました"
    else
        print_info "ECSクラスター 'evox-cluster' は既に存在します"
    fi
}

# タスク定義の作成
create_task_definitions() {
    print_info "ECSタスク定義を作成中..."
    
    # バックエンドタスク定義
    cat > "$SCRIPT_DIR/backend-task-definition.json" << EOF
{
    "family": "evox-backend",
    "networkMode": "awsvpc",
    "requiresCompatibilities": ["FARGATE"],
    "cpu": "256",
    "memory": "512",
    "executionRoleArn": "arn:aws:iam::$AWS_ACCOUNT_ID:role/ecsTaskExecutionRole",
    "containerDefinitions": [
        {
            "name": "evox-backend",
            "image": "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/evox-backend:latest",
            "portMappings": [
                {
                    "containerPort": 8000,
                    "protocol": "tcp"
                }
            ],
            "environment": [
                {"name": "APP_ENV", "value": "production"},
                {"name": "DB_HOST", "value": "$RDS_ENDPOINT"},
                {"name": "REDIS_HOST", "value": "$REDIS_ENDPOINT"}
            ],
            "secrets": [
                {"name": "DB_PASSWORD", "valueFrom": "arn:aws:secretsmanager:$AWS_REGION:$AWS_ACCOUNT_ID:secret:evox/db-password"},
                {"name": "TWILIO_AUTH_TOKEN", "valueFrom": "arn:aws:secretsmanager:$AWS_REGION:$AWS_ACCOUNT_ID:secret:evox/twilio-auth-token"}
            ],
            "logConfiguration": {
                "logDriver": "awslogs",
                "options": {
                    "awslogs-group": "/ecs/evox-backend",
                    "awslogs-region": "$AWS_REGION",
                    "awslogs-stream-prefix": "ecs"
                }
            }
        }
    ]
}
EOF
    
    # フロントエンドタスク定義
    cat > "$SCRIPT_DIR/frontend-task-definition.json" << EOF
{
    "family": "evox-frontend",
    "networkMode": "awsvpc",
    "requiresCompatibilities": ["FARGATE"],
    "cpu": "256",
    "memory": "512",
    "executionRoleArn": "arn:aws:iam::$AWS_ACCOUNT_ID:role/ecsTaskExecutionRole",
    "containerDefinitions": [
        {
            "name": "evox-frontend",
            "image": "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/evox-frontend:latest",
            "portMappings": [
                {
                    "containerPort": 3000,
                    "protocol": "tcp"
                }
            ],
            "environment": [
                {"name": "VITE_API_BASE_URL", "value": "https://$DOMAIN_NAME/api"}
            ],
            "logConfiguration": {
                "logDriver": "awslogs",
                "options": {
                    "awslogs-group": "/ecs/evox-frontend",
                    "awslogs-region": "$AWS_REGION",
                    "awslogs-stream-prefix": "ecs"
                }
            }
        }
    ]
}
EOF
    
    # タスク定義を登録
    aws ecs register-task-definition --cli-input-json file://"$SCRIPT_DIR/backend-task-definition.json"
    aws ecs register-task-definition --cli-input-json file://"$SCRIPT_DIR/frontend-task-definition.json"
    print_success "タスク定義を作成しました"
}

# サービスを作成
create_services() {
    print_info "ECSサービスを作成中..."
    
    # バックエンドサービス
    aws ecs create-service \
        --cluster evox-cluster \
        --service-name evox-backend-service \
        --task-definition evox-backend \
        --desired-count 1 \
        --launch-type FARGATE \
        --network-configuration "awsvpcConfiguration={subnets=[$SUBNET_IDS],securityGroups=[$SECURITY_GROUP_IDS],assignPublicIp=ENABLED}" \
        --load-balancers "targetGroupArn=$BACKEND_TARGET_GROUP_ARN,containerName=evox-backend,containerPort=8000"
    
    # フロントエンドサービス
    aws ecs create-service \
        --cluster evox-cluster \
        --service-name evox-frontend-service \
        --task-definition evox-frontend \
        --desired-count 1 \
        --launch-type FARGATE \
        --network-configuration "awsvpcConfiguration={subnets=[$SUBNET_IDS],securityGroups=[$SECURITY_GROUP_IDS],assignPublicIp=ENABLED}" \
        --load-balancers "targetGroupArn=$FRONTEND_TARGET_GROUP_ARN,containerName=evox-frontend,containerPort=3000"
    
    print_success "ECSサービスを作成しました"
}

# メイン処理
main() {
    check_requirements
    check_aws_credentials
    create_ecr_repositories
    build_and_push_images
    create_ecs_cluster
    create_task_definitions
    create_services
    
    print_success "=== AWSデプロイ完了 ==="
    print_info "アプリケーションURL: https://$DOMAIN_NAME"
    print_info "ECSコンソール: https://console.aws.amazon.com/ecs/home?region=$AWS_REGION#/clusters/evox-cluster"
    print_info "CloudWatchログ: https://console.aws.amazon.com/cloudwatch/home?region=$AWS_REGION#logsV2:log-groups"
}

# スクリプト実行
main "$@"
