<?php

require_once 'vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 自動管理画面ログインテスト ===\n\n";

// ChromeDriverのパスを設定（Macの場合）
$chromeDriverPath = '/usr/local/bin/chromedriver';
if (!file_exists($chromeDriverPath)) {
    $chromeDriverPath = '/opt/homebrew/bin/chromedriver';
}

if (!file_exists($chromeDriverPath)) {
    echo "❌ ChromeDriverが見つかりません。以下のコマンドでインストールしてください：\n";
    echo "brew install chromedriver\n";
    exit(1);
}

try {
    // ChromeDriverを起動
    echo "1. ChromeDriverを起動中...\n";
    putenv("webdriver.chrome.driver=" . $chromeDriverPath);
    
    $options = new ChromeOptions();
    $options->addArguments([
        '--headless',  // ヘッドレスモード（画面表示なし）
        '--no-sandbox',
        '--disable-dev-shm-usage',
        '--disable-gpu',
        '--window-size=1920,1080'
    ]);
    
    $capabilities = DesiredCapabilities::chrome();
    $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
    
    $driver = ChromeDriver::start($capabilities);
    echo "✅ ChromeDriver起動成功\n\n";
    
    // 2. ログインページにアクセス
    echo "2. ログインページにアクセス中...\n";
    $driver->get('http://localhost:8000/admin-ai8edq64p2i5');
    
    // ページが読み込まれるまで待機
    $driver->wait(10)->until(
        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('adminPhone'))
    );
    echo "✅ ログインページアクセス成功\n\n";
    
    // 3. 電話番号とパスワードを入力
    echo "3. 認証情報を入力中...\n";
    $phoneInput = $driver->findElement(WebDriverBy::id('adminPhone'));
    $phoneInput->sendKeys('8090330374'); // +81は自動で付与される
    
    $passwordInput = $driver->findElement(WebDriverBy::id('adminPassword'));
    $passwordInput->sendKeys('admin123');
    
    echo "✅ 認証情報入力完了\n\n";
    
    // 4. 認証コード発行ボタンをクリック
    echo "4. 認証コードを発行中...\n";
    $loginButton = $driver->findElement(WebDriverBy::cssSelector('#step1 .login-btn'));
    $loginButton->click();
    
    // OTP入力画面が表示されるまで待機
    $driver->wait(10)->until(
        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#step2'))
    );
    echo "✅ 認証コード発行成功\n\n";
    
    // 5. データベースからOTPコードを取得
    echo "5. OTPコードをデータベースから取得中...\n";
    try {
        $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=evox', 'tsuburayatoshifumi', '');
        $stmt = $pdo->prepare("SELECT adminopt_code FROM otp_codes_admin WHERE adminopt_temp_id LIKE 'test_%' ORDER BY adminopt_created_at DESC LIMIT 1");
        $stmt->execute();
        $otpCode = $stmt->fetchColumn();
        
        if ($otpCode) {
            echo "✅ OTPコード取得成功: " . $otpCode . "\n\n";
        } else {
            echo "❌ OTPコードが見つかりません\n";
            exit(1);
        }
    } catch (Exception $e) {
        echo "❌ データベース接続エラー: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    // 6. OTPコードを入力
    echo "6. OTPコードを入力中...\n";
    $otpInputs = $driver->findElements(WebDriverBy::cssSelector('.otp-input'));
    
    if (count($otpInputs) === 6) {
        for ($i = 0; $i < 6; $i++) {
            $otpInputs[$i]->sendKeys($otpCode[$i]);
        }
        echo "✅ OTPコード入力完了\n\n";
    } else {
        echo "❌ OTP入力フィールドが見つかりません\n";
        exit(1);
    }
    
    // 7. 認証ボタンをクリック
    echo "7. 認証を実行中...\n";
    $verifyButton = $driver->findElement(WebDriverBy::cssSelector('#step2 .login-btn'));
    $verifyButton->click();
    
    // 8. ダッシュボードへのリダイレクトを待機
    echo "8. ダッシュボードへのリダイレクトを待機中...\n";
    
    // 最大30秒待機
    $maxWaitTime = 30;
    $startTime = time();
    $dashboardLoaded = false;
    
    while (time() - $startTime < $maxWaitTime) {
        $currentUrl = $driver->getCurrentURL();
        echo "現在のURL: " . $currentUrl . "\n";
        
        if (strpos($currentUrl, '/admin/dashboard') !== false) {
            $dashboardLoaded = true;
            break;
        }
        
        sleep(2);
    }
    
    if ($dashboardLoaded) {
        echo "✅ ダッシュボードアクセス成功\n\n";
        
        // 9. ダッシュボードの内容を確認
        echo "9. ダッシュボードの内容を確認中...\n";
        
        // ページが完全に読み込まれるまで待機
        $driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('body'))
        );
        
        $pageSource = $driver->getPageSource();
        
        if (strpos($pageSource, 'EvoX 管理画面') !== false) {
            echo "✅ ダッシュボードの内容確認成功\n";
        } else {
            echo "⚠️ ダッシュボードの内容に問題がある可能性があります\n";
        }
        
        // 10. トークンの状況を確認
        echo "\n10. トークンの状況を確認中...\n";
        
        // JavaScriptでトークンの状況を取得
        $tokenStatus = $driver->executeScript("
            return {
                localStorageToken: localStorage.getItem('admin_token') ? 'present' : 'missing',
                hasAdminTokenCookie: document.cookie.includes('admin_token='),
                allCookies: document.cookie
            };
        ");
        
        echo "localStorageトークン: " . $tokenStatus['localStorageToken'] . "\n";
        echo "Cookieトークン: " . ($tokenStatus['hasAdminTokenCookie'] ? 'present' : 'missing') . "\n";
        echo "全Cookie: " . $tokenStatus['allCookies'] . "\n";
        
        if ($tokenStatus['localStorageToken'] === 'present' && $tokenStatus['hasAdminTokenCookie']) {
            echo "✅ トークンが正常に保存されています\n";
        } else {
            echo "⚠️ トークンの保存に問題があります\n";
        }
        
    } else {
        echo "❌ ダッシュボードへのリダイレクトが失敗しました\n";
        echo "最終URL: " . $driver->getCurrentURL() . "\n";
        
        // エラーメッセージがあれば表示
        try {
            $errorElement = $driver->findElement(WebDriverBy::id('otpErrorMessage'));
            $errorMessage = $errorElement->getText();
            if ($errorMessage) {
                echo "エラーメッセージ: " . $errorMessage . "\n";
            }
        } catch (Exception $e) {
            // エラーメッセージ要素が見つからない場合は無視
        }
    }
    
    // 11. スクリーンショットを保存（デバッグ用）
    echo "\n11. スクリーンショットを保存中...\n";
    $screenshotPath = storage_path('logs/admin_test_screenshot.png');
    $driver->takeScreenshot($screenshotPath);
    echo "✅ スクリーンショット保存: " . $screenshotPath . "\n";
    
} catch (Exception $e) {
    echo "❌ エラーが発生しました: " . $e->getMessage() . "\n";
    echo "スタックトレース:\n" . $e->getTraceAsString() . "\n";
} finally {
    // ブラウザを閉じる
    if (isset($driver)) {
        $driver->quit();
        echo "\n✅ ブラウザを閉じました\n";
    }
}

echo "\n=== テスト完了 ===\n";

if (isset($dashboardLoaded) && $dashboardLoaded) {
    echo "🎉 管理画面のログイン機能は正常に動作しています！\n";
} else {
    echo "❌ 管理画面のログイン機能に問題があります。\n";
    echo "詳細は上記のログとスクリーンショットを確認してください。\n";
}
