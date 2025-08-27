#!/bin/bash

echo "=== Twilio設定に切り替え ==="
echo "このスクリプトは本番環境でTwilio SMS送信を有効にします。"
echo ""

# 現在の設定を確認
echo "現在のSMS_PROVIDER設定:"
grep "SMS_PROVIDER" .env

echo ""
read -p "Twilioに切り替えますか？ (y/N): " confirm

if [[ $confirm =~ ^[Yy]$ ]]; then
    echo ""
    echo "Twilio設定に切り替え中..."
    
    # SMS_PROVIDERをtwilioに変更
    sed -i '' 's/SMS_PROVIDER=mock/SMS_PROVIDER=twilio/' .env
    
    echo "✅ SMS_PROVIDERをtwilioに変更しました"
    echo ""
    echo "新しい設定:"
    grep "SMS_PROVIDER" .env
    echo ""
    echo "⚠️  注意事項:"
    echo "1. Twilioアカウントに十分なクレジットがあることを確認してください"
    echo "2. 電話番号が認証済みであることを確認してください"
    echo "3. 実際のSMS送信が発生し、料金がかかります"
    echo ""
    echo "開発環境に戻す場合は以下を実行してください:"
    echo "sed -i '' 's/SMS_PROVIDER=twilio/SMS_PROVIDER=mock/' .env"
    
else
    echo "キャンセルしました。現在の設定（mock）を維持します。"
fi
