<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EvoX 管理画面 - ログイン</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
        }

        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: none;
        }

        .success-message {
            background: #efe;
            color: #3c3;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: none;
        }

        .otp-section {
            display: none;
        }

        .otp-section.show {
            display: block !important;
        }

        .otp-inputs {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.2rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            ime-mode: disabled;
        }

        .otp-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .temp-id {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 5px;
            text-align: center;
            font-family: monospace;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            color: #666;
        }

        .phone-input-container {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .country-select {
            flex: 0 0 auto;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
            width: 120px;
            min-width: 120px;
        }

        .country-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .phone-input {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            ime-mode: disabled;
        }

        .phone-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 1rem;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>EvoX 管理画面</h1>
            <p>管理者ログイン</p>
        </div>

        <!-- ステップ1: 電話番号・パスワード入力 -->
        <div id="step1">
            <div class="temp-id" id="tempIdDisplay"></div>
            
            <div class="form-group">
                <label for="adminPhone">電話番号</label>
                <div class="phone-input-container">
                    <select id="adminCountryCode" class="country-select">
                        <option value="+81" selected>🇯🇵 +81</option>
                    </select>
                    <input type="tel" id="adminPhone" class="phone-input" placeholder="09012345678" required inputmode="numeric" pattern="[0-9]*" style="ime-mode: disabled;">
                </div>
            </div>

            <div class="form-group">
                <label for="adminPassword">パスワード</label>
                <input type="password" id="adminPassword" placeholder="パスワードを入力" required>
            </div>

            <button class="login-btn" onclick="login()" id="loginButton">認証コードを発行</button>
            
            <div class="error-message" id="errorMessage"></div>
        </div>

        <!-- ステップ2: OTP認証 -->
        <div id="step2" class="otp-section">
            <div class="temp-id" id="tempIdDisplay2"></div>
            
            <p style="text-align: center; margin-bottom: 1rem; color: #666;">
                携帯番号に届いたコードを入力しましょう
            </p>

            <div class="otp-inputs">
                <input type="text" class="otp-input" maxlength="1" data-index="0" inputmode="numeric" pattern="[0-9]*" style="ime-mode: disabled;">
                <input type="text" class="otp-input" maxlength="1" data-index="1" inputmode="numeric" pattern="[0-9]*" style="ime-mode: disabled;">
                <input type="text" class="otp-input" maxlength="1" data-index="2" inputmode="numeric" pattern="[0-9]*" style="ime-mode: disabled;">
                <input type="text" class="otp-input" maxlength="1" data-index="3" inputmode="numeric" pattern="[0-9]*" style="ime-mode: disabled;">
                <input type="text" class="otp-input" maxlength="1" data-index="4" inputmode="numeric" pattern="[0-9]*" style="ime-mode: disabled;">
                <input type="text" class="otp-input" maxlength="1" data-index="5" inputmode="numeric" pattern="[0-9]*" style="ime-mode: disabled;">
            </div>

            <button class="login-btn" onclick="verifyOtp()">認証する</button>
            
            <div class="error-message" id="otpErrorMessage"></div>
            <div class="success-message" id="otpSuccessMessage"></div>
        </div>
    </div>

    <script>
        // 全角数字を半角数字に変換する関数
        function convertToHalfWidth(input) {
            if (!input || !input.value) return;
            
            const value = input.value;
            // 全角数字を半角数字に変換
            let converted = value.replace(/[０-９]/g, function(s) {
                return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
            });
            // 数字以外の文字を除去
            converted = converted.replace(/[^0-9]/g, '');
            // 最大1文字に制限
            converted = converted.substring(0, 1);
            
            if (value !== converted) {
                input.value = converted;
            }
            
            // 次の入力フィールドにフォーカス
            if (converted && input.nextElementSibling && input.nextElementSibling.classList.contains('otp-input')) {
                input.nextElementSibling.focus();
            }
        }
        
        let tempId = '';
        let currentStep = 1;
        let fullPhone = ''; // グローバル変数として追加
        let isSubmitting = false; // 重複送信防止フラグ

        // ページ読み込み時に一時IDを生成
        window.onload = function() {
            tempId = generateTempId();
            document.getElementById('tempIdDisplay').textContent = `一時ID: ${tempId}`;
            document.getElementById('tempIdDisplay2').textContent = `一時ID: ${tempId}`;
            
            // 電話番号入力フィールドの設定
            setupPhoneInput();
            
            // OTP入力フィールドの設定
            setupOtpInputs();
            
            // フォーム送信の重複防止
            setupFormProtection();
        };

        // 電話番号入力フィールドの設定
        function setupPhoneInput() {
            const phoneInput = document.getElementById('adminPhone');
            
            // IME制御と数字変換
            phoneInput.addEventListener('focus', forceNumericInput);
            phoneInput.addEventListener('mousedown', forceNumericInput);
            phoneInput.addEventListener('input', convertToHalfWidth);
            phoneInput.addEventListener('paste', handlePhonePaste);
        }
        
        // OTP入力フィールドの設定
        function setupOtpInputs() {
            const otpInputs = document.querySelectorAll('.otp-input');
            
            otpInputs.forEach((input, index) => {
                // フォーカス時にIMEを無効化
                input.addEventListener('focus', () => {
                    input.setAttribute('inputmode', 'numeric');
                    input.setAttribute('pattern', '[0-9]*');
                    input.style.imeMode = 'disabled';
                });
                
                // 入力時の処理
                input.addEventListener('input', (event) => {
                    let value = event.target.value;
                    // 全角数字を半角数字に変換
                    value = value.replace(/[０-９]/g, function(s) {
                        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
                    });
                    // 数字以外の文字を除去
                    value = value.replace(/[^0-9]/g, '');
                    // 最大1文字に制限
                    value = value.substring(0, 1);
                    
                    if (event.target.value !== value) {
                        event.target.value = value;
                    }
                    
                    // 次の入力フィールドにフォーカス
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });
                
                // キーダウン時の処理（バックスペースで前のフィールドに移動）
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });
        }

        // 数字入力に強制
        function forceNumericInput() {
            const phoneInput = document.getElementById('adminPhone');
            phoneInput.setAttribute('inputmode', 'numeric');
            phoneInput.setAttribute('pattern', '[0-9]*');
            phoneInput.style.imeMode = 'disabled';
            phoneInput.style.imeMode = 'disabled'; // 追加
        }

        // 全角数字を半角に変換
        function convertToHalfWidth(event) {
            let value = event.target.value;
            
            // 全角数字を半角に変換
            value = value.replace(/[０-９]/g, function(s) {
                return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
            });
            
            // 漢数字を半角数字に変換
            const kanjiToNumber = {
                '一': '1', '二': '2', '三': '3', '四': '4', '五': '5',
                '六': '6', '七': '7', '八': '8', '九': '9', '零': '0'
            };
            
            for (let kanji in kanjiToNumber) {
                value = value.replace(new RegExp(kanji, 'g'), kanjiToNumber[kanji]);
            }
            
            // 数字以外の文字を除去
            value = value.replace(/[^0-9]/g, '');
            
            event.target.value = value;
        }

        // フォーム送信の重複防止設定
        function setupFormProtection() {
            // Enterキーでの送信を防止
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && isSubmitting) {
                    event.preventDefault();
                    console.log('Form submission prevented - already submitting');
                }
            });
            
            // ダブルクリック防止
            const loginBtn = document.getElementById('loginButton');
            if (loginBtn) {
                loginBtn.addEventListener('dblclick', function(event) {
                    event.preventDefault();
                    console.log('Double click prevented');
                });
            }
        }

        // ペースト時の処理
        function handlePhonePaste(event) {
            event.preventDefault();
            let pastedText = (event.clipboardData || window.clipboardData).getData('text');
            
            // 全角数字を半角に変換
            pastedText = pastedText.replace(/[０-９]/g, function(s) {
                return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
            });
            
            // 漢数字を半角数字に変換
            const kanjiToNumber = {
                '一': '1', '二': '2', '三': '3', '四': '4', '五': '5',
                '六': '6', '七': '7', '八': '8', '九': '9', '零': '0'
            };
            
            for (let kanji in kanjiToNumber) {
                pastedText = pastedText.replace(new RegExp(kanji, 'g'), kanjiToNumber[kanji]);
            }
            
            // 数字以外の文字を除去
            pastedText = pastedText.replace(/[^0-9]/g, '');
            
            document.getElementById('adminPhone').value = pastedText;
        }

        // 13文字の一時IDを生成
        function generateTempId() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < 13; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        // ログイン処理
        async function login() {
            // 重複送信防止
            if (isSubmitting) {
                console.log('Login already in progress, ignoring duplicate request');
                return;
            }

            const countryCode = document.getElementById('adminCountryCode').value;
            const phoneNumber = document.getElementById('adminPhone').value;
            const password = document.getElementById('adminPassword').value;

            if (!phoneNumber || !password) {
                showError('電話番号とパスワードを入力してください。');
                return;
            }

            // 電話番号を正規化
            let phone = phoneNumber.replace(/\D/g, '');
            
            // 日本の場合、先頭の0を削除
            if (countryCode === '+81' && phone.startsWith('0')) {
                phone = phone.substring(1);
            }
            
            fullPhone = countryCode + phone; // グローバル変数に保存

            // デバッグログ
            console.log('Login attempt:', {
                countryCode: countryCode,
                phoneNumber: phoneNumber,
                phone: phone,
                fullPhone: fullPhone,
                password: password
            });

            const loginBtn = document.querySelector('#step1 .login-btn');
            loginBtn.disabled = true;
            loginBtn.textContent = '送信中...';
            isSubmitting = true; // 送信開始フラグを設定

            try {
                const response = await fetch('/api/admin/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        admin_phone: fullPhone,
                        admin_password: password,
                        temp_id: tempId
                    })
                });

                console.log('Raw response:', response);
                console.log('Response headers:', response.headers);

                let data;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    const responseText = await response.text();
                    console.log('Response text:', responseText);
                    showError('レスポンスの解析に失敗しました。');
                    return;
                }

                // デバッグログ
                console.log('Login response:', {
                    status: response.status,
                    statusText: response.statusText,
                    data: data,
                    dataType: typeof data,
                    dataKeys: Object.keys(data)
                });
                console.log('Data content:', JSON.stringify(data, null, 2));

                if (response.ok) {
                    console.log('Login successful, proceeding to OTP step');
                    showSuccess('認証コードを送信しました。');
                    currentStep = 2;
                    
                    // DOM操作のデバッグ
                    const step1 = document.getElementById('step1');
                    const step2 = document.getElementById('step2');
                    console.log('Step1 element:', step1);
                    console.log('Step2 element:', step2);
                    
                    if (step1 && step2) {
                        step1.style.display = 'none';
                        step2.style.display = 'block';
                        step2.classList.add('show');
                        console.log('DOM elements updated successfully');
                    } else {
                        console.error('Step elements not found');
                    }
                    
                    setupOtpInputs();
                } else {
                    console.log('Login failed:', data.message);
                    showError(data.message || 'ログインに失敗しました。');
                }
            } catch (error) {
                console.error('Login error:', error);
                showError('通信エラーが発生しました。');
            } finally {
                loginBtn.disabled = false;
                loginBtn.textContent = '認証コードを発行';
                isSubmitting = false; // 送信完了フラグをリセット
            }
        }

        // OTP認証
        function verifyOtp() {
            const inputs = document.querySelectorAll('.otp-input');
            let otpCode = Array.from(inputs).map(input => input.value).join('');
            
            // 全角数字を半角数字に変換
            otpCode = otpCode.replace(/[０-９]/g, function(s) {
                return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
            });

            console.log('OTP verification attempt:', {
                otpCode: otpCode,
                otpCodeLength: otpCode.length,
                tempId: tempId,
                fullPhone: fullPhone
            });

            if (otpCode.length !== 6) {
                showOtpError('6桁のコードを入力してください。');
                return;
            }

            const verifyBtn = document.querySelector('#step2 .login-btn');
            verifyBtn.disabled = true;
            verifyBtn.textContent = '認証中...';

            // フォームを作成してPOST送信
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/api/admin/auth/verify-otp';

            
            // CSRFトークンを追加
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);
            

            
            // データを追加
            const fields = {
                'temp_id': tempId,
                'otp_code': otpCode,
                'admin_phone': fullPhone
            };
            
            Object.keys(fields).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            
            // フォームデータを取得してfetchで送信
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('OTP verification response:', data);
                if (data.success) {
                    console.log('Authentication successful, redirecting to dashboard...');
                    
                    // セッションベース認証なので、直接ダッシュボードにリダイレクト
                    setTimeout(() => {
                        window.location.href = '/admin/dashboard';
                    }, 500);
                } else {
                    showOtpError(data.message || '認証に失敗しました。');
                }
            })
            .catch(error => {
                console.error('OTP verification error:', error);
                showOtpError('通信エラーが発生しました。');
            });
        }

        // エラーメッセージ表示
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
        }

        // 成功メッセージ表示
        function showSuccess(message) {
            console.log('Showing success message:', message);
            const errorElement = document.getElementById('errorMessage');
            if (errorElement) {
                errorElement.textContent = '';
                console.log('Success message cleared from error element');
            } else {
                console.error('Error message element not found');
            }
            // 成功メッセージは一時的に表示
            setTimeout(() => {
                if (errorElement) {
                    errorElement.textContent = '';
                    console.log('Success message timeout cleared');
                }
            }, 3000);
        }

        // OTPエラーメッセージ表示
        function showOtpError(message) {
            document.getElementById('otpErrorMessage').textContent = message;
            document.getElementById('otpSuccessMessage').textContent = '';
        }

        // OTP成功メッセージ表示
        function showOtpSuccess(message) {
            document.getElementById('otpSuccessMessage').textContent = message;
            document.getElementById('otpErrorMessage').textContent = '';
        }
    </script>
</body>
</html>



