# QRコードオプション機能 実装仕様書

## 概要
イベントQRコード読み込み後のユーザー体験を向上させるため、アバターの表情、行動、背景色、エフェクト、サウンドの5種類のオプション選択機能を実装しました。

## 1. データベース設計

### 1.1 QRコードテーブル拡張
```sql
-- オプション項目（JSON形式で保存）
qr_avatar_expressions    JSON    -- アバター表情オプション（最大5種類）
qr_avatar_actions        JSON    -- アバター行動オプション（最大5種類）
qr_background_colors     JSON    -- 背景色オプション（最大5種類）
qr_effects              JSON    -- エフェクトオプション（最大5種類）
qr_sounds               JSON    -- サウンドオプション（最大5種類）
qr_options_enabled      BOOLEAN -- オプション機能の有効/無効フラグ
```

### 1.2 QR使用履歴テーブル拡張
```sql
-- ユーザー選択結果
qruse_selected_expression    VARCHAR(100) -- 選択されたアバター表情
qruse_selected_action        VARCHAR(100) -- 選択されたアバター行動
qruse_selected_background    VARCHAR(100) -- 選択された背景色
qruse_selected_effect        VARCHAR(100) -- 選択されたエフェクト
qruse_selected_sound         VARCHAR(100) -- 選択されたサウンド
qruse_options_selected_at    TIMESTAMP   -- オプション選択時刻
```

## 2. デフォルトオプション選択肢

### 2.1 アバター表情（5種類）
- 笑顔
- 怒り
- 悲しみ
- 驚き
- 普通

### 2.2 アバター行動（5種類）
- ダンス
- ジャンプ
- 手を振る
- 座る
- 走る

### 2.3 背景色（5種類）
- 青空
- 夕日
- 星空
- 森
- 海

### 2.4 エフェクト（5種類）
- キラキラ
- 虹
- 花火
- 雪
- 雨

### 2.5 サウンド（5種類）
- ファンファーレ
- 拍手
- 音楽
- 効果音
- 無音

## 3. 管理画面機能

### 3.1 QRコード作成・編集画面
- **オプション設定セクション**: チェックボックスで有効/無効切り替え
- **動的フォーム**: 最大5種類まで追加・削除可能
- **リアルタイム制御**: JavaScriptでオプション項目の管理

### 3.2 オプション管理画面（専用）
**URL**: `/admin/qrcode-options`

#### 3.2.1 デフォルト選択肢タブ
- 現在のデフォルト選択肢を表示
- 各カテゴリ別に整理された一覧表示

#### 3.2.2 カスタム選択肢タブ
- **動的追加**: 最大10種類まで追加可能
- **個別削除**: 不要な選択肢を削除
- **一括更新**: オプション有効な全QRコードに適用

#### 3.2.3 一括操作タブ
- **デフォルト適用**: 選択したQRコードにデフォルトオプションを適用
- **有効/無効化**: オプション機能の一括制御

### 3.3 QRコード一覧画面
- **オプション管理ボタン**: 専用管理画面へのアクセス
- **オプション状態表示**: 各QRコードのオプション有効/無効状態
- **選択肢数表示**: 設定されている選択肢の種類数

### 3.4 QRコード詳細画面
- **オプション情報表示**: 設定されたオプションの一覧表示
- **視覚的整理**: カテゴリ別にグループ化

## 4. バックエンド実装

### 4.1 コントローラー
```php
// QRコードコントローラー（拡張）
class QrCodeController extends Controller
{
    public function store(Request $request)    // オプション機能対応
    public function update(Request $request)   // オプション機能対応
}

// オプション管理コントローラー（新規）
class QrCodeOptionsController extends Controller
{
    public function index()                    // 管理画面表示
    public function applyDefaults()            // デフォルト適用
    public function updateCustomOptions()      // カスタム更新
    public function toggleOptions()            // 一括有効/無効化
}
```

### 4.2 モデル
```php
// QRコードモデル（拡張）
class QrCode extends Model
{
    protected $fillable = [
        // 既存フィールド...
        'qr_avatar_expressions',
        'qr_avatar_actions',
        'qr_background_colors',
        'qr_effects',
        'qr_sounds',
        'qr_options_enabled',
    ];

    protected $casts = [
        // 既存キャスト...
        'qr_avatar_expressions' => 'array',
        'qr_avatar_actions' => 'array',
        'qr_background_colors' => 'array',
        'qr_effects' => 'array',
        'qr_sounds' => 'array',
        'qr_options_enabled' => 'boolean',
    ];
}

// QR使用履歴モデル（拡張）
class QrUseList extends Model
{
    protected $fillable = [
        // 既存フィールド...
        'qruse_selected_expression',
        'qruse_selected_action',
        'qruse_selected_background',
        'qruse_selected_effect',
        'qruse_selected_sound',
        'qruse_options_selected_at',
    ];
}
```

### 4.3 バリデーション
```php
// オプション関連バリデーション
'qr_options_enabled' => 'boolean',
'qr_avatar_expressions' => 'nullable|array|max:5',
'qr_avatar_expressions.*' => 'nullable|string|max:100',
'qr_avatar_actions' => 'nullable|array|max:5',
'qr_avatar_actions.*' => 'nullable|string|max:100',
'qr_background_colors' => 'nullable|array|max:5',
'qr_background_colors.*' => 'nullable|string|max:100',
'qr_effects' => 'nullable|array|max:5',
'qr_effects.*' => 'nullable|string|max:100',
'qr_sounds' => 'nullable|array|max:5',
'qr_sounds.*' => 'nullable|string|max:100'
```

## 5. フロントエンド機能

### 5.1 JavaScript機能
```javascript
// オプション機能の表示/非表示切り替え
function toggleOptionsSection(type)

// オプション項目の追加
function addOption(containerId, name, label)

// オプション項目の削除
function removeOption(button)

// カスタムオプションの追加
function addCustomOption(containerId, name, label)

// カスタムオプションの削除
function removeCustomOption(button)
```

### 5.2 UI/UX特徴
- **直感的な操作**: チェックボックスで有効/無効切り替え
- **動的フォーム**: 最大5種類まで動的に追加
- **視覚的フィードバック**: カード形式で整理、アイコンで視覚化
- **レスポンシブデザイン**: Bootstrap 4のグリッドシステム

## 6. ルーティング

### 6.1 管理画面ルート
```php
// QRコードオプション管理
Route::get('/admin/qrcode-options', [QrCodeOptionsController::class, 'index'])
    ->middleware('admin')
    ->name('admin.qrcode-options.index');

Route::post('/admin/qrcode-options/apply-defaults', [QrCodeOptionsController::class, 'applyDefaults'])
    ->middleware('admin')
    ->name('admin.qrcode-options.apply-defaults');

Route::post('/admin/qrcode-options/update-custom', [QrCodeOptionsController::class, 'updateCustomOptions'])
    ->middleware('admin')
    ->name('admin.qrcode-options.update-custom');

Route::post('/admin/qrcode-options/toggle', [QrCodeOptionsController::class, 'toggleOptions'])
    ->middleware('admin')
    ->name('admin.qrcode-options.toggle');
```

## 7. データ処理

### 7.1 オプション配列処理
```php
// 空の値を除去
$avatarExpressions = $request->qr_avatar_expressions ? array_filter($request->qr_avatar_expressions) : null;
$avatarActions = $request->qr_avatar_actions ? array_filter($request->qr_avatar_actions) : null;
$backgroundColors = $request->qr_background_colors ? array_filter($request->qr_background_colors) : null;
$effects = $request->qr_effects ? array_filter($request->qr_effects) : null;
$sounds = $request->qr_sounds ? array_filter($request->qr_sounds) : null;
```

### 7.2 一括更新処理
```php
// オプションが有効なQRコードを一括更新
$updatedCount = QrCode::where('qr_options_enabled', true)->update($customOptions);
```

## 8. 統計情報

### 8.1 オプション管理画面統計
- **総QRコード数**: 全体のQRコード数
- **オプション有効**: オプション機能が有効なQRコード数
- **オプション無効**: オプション機能が無効なQRコード数

### 8.2 QRコード一覧統計
- **リアルイベント総数**: リアルイベントQRコード数
- **アクティブ**: アクティブなQRコード数
- **非アクティブ**: 非アクティブなQRコード数
- **ライブイベント**: ライブイベントQRコード数

## 9. セキュリティ

### 9.1 認証・認可
- **管理者認証**: セッションベース認証
- **ミドルウェア**: `admin`ミドルウェアでアクセス制御
- **CSRF保護**: 全フォームでCSRFトークン検証

### 9.2 データ検証
- **入力値検証**: バリデーションルールによる厳密な検証
- **SQLインジェクション対策**: Eloquent ORMによる安全なクエリ
- **XSS対策**: Bladeテンプレートエンジンによる自動エスケープ

## 10. パフォーマンス

### 10.1 データベース最適化
- **インデックス**: 適切なインデックス設定
- **クエリ最適化**: N+1問題の回避
- **一括更新**: 効率的なデータベース更新

### 10.2 フロントエンド最適化
- **非同期処理**: JavaScriptによる動的UI更新
- **キャッシュ**: 適切なキャッシュ戦略
- **レスポンシブ**: モバイル対応の最適化

## 11. 今後の拡張予定

### 11.1 フロントエンド連携
- **API設計**: オプション選択用APIの実装
- **選択UI**: ユーザー向け選択画面の作成
- **結果保存**: 選択結果のデータベース保存

### 11.2 拡張機能
- **テーマ別選択肢**: イベントテーマに応じた選択肢
- **季節限定**: 季節に応じた選択肢の自動切り替え
- **統計分析**: 選択傾向の分析・レポート機能

## 12. テスト項目

### 12.1 機能テスト
1. **オプション作成**: 管理画面でオプション設定
2. **データ保存**: データベースへの正しい保存
3. **編集機能**: 既存オプションの編集
4. **表示確認**: 詳細画面でのオプション表示

### 12.2 統合テスト
1. **一括操作**: 複数QRコードへの一括適用
2. **バリデーション**: 入力値の検証
3. **エラーハンドリング**: 異常系の処理

## 13. 運用・保守

### 13.1 ログ管理
- **操作ログ**: オプション変更の履歴記録
- **エラーログ**: 異常時の詳細ログ
- **アクセスログ**: 管理画面へのアクセス記録

### 13.2 バックアップ
- **データベース**: 定期的なバックアップ
- **設定ファイル**: 設定変更の履歴管理
- **復旧手順**: 障害時の復旧手順書

---

**作成日**: 2025年8月25日  
**バージョン**: 1.0  
**作成者**: AI Assistant  
**最終更新**: 2025年8月25日
