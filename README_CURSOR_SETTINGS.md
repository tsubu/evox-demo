# Cursor 安全なファイル削除設定

このプロジェクトでは、ファイルの削除を禁止し、代わりに`.trash`フォルダに移動する安全なシステムを実装しています。

## 設定内容

### 1. ファイル削除の確認
- すべてのファイル削除操作で確認ダイアログが表示されます
- 誤削除を防ぐための安全機能が有効になっています

### 2. 安全な削除システム
- ファイルは削除されず、`.trash`フォルダに移動されます
- タイムスタンプ付きのファイル名で保存され、重複を避けます

## 使用方法

### キーボードショートカット
- `Ctrl+Shift+Delete`: 現在のファイルを安全に削除（.trashに移動）
- `Ctrl+Shift+T`: .trashフォルダの内容を表示
- `Ctrl+Shift+E`: .trashフォルダを空にする

### タスク実行
1. `Ctrl+Shift+P`でコマンドパレットを開く
2. "Tasks: Run Task"を選択
3. 以下のタスクから選択：
   - `Safe Delete File`: 単一ファイルを安全に削除
   - `Safe Delete Multiple Files`: 複数ファイルを安全に削除
   - `Show Trash Contents`: .trashフォルダの内容を表示
   - `Empty Trash`: .trashフォルダを空にする

## ファイル構造

```
.vscode/
├── settings.json      # Cursorの基本設定
├── tasks.json         # カスタムタスク定義
├── keybindings.json   # キーボードショートカット
└── extensions.json    # 推奨拡張機能

scripts/
└── safe-delete.sh     # 安全な削除スクリプト

.trash/                # 削除されたファイルの保存場所
```

## 注意事項

- `.trash`フォルダは`.gitignore`に含まれているため、Gitにコミットされません
- 削除されたファイルは手動で復元できます
- 定期的に`.trash`フォルダを空にすることをお勧めします

## トラブルシューティング

### スクリプトが実行できない場合
```bash
chmod +x scripts/safe-delete.sh
```

### 設定が反映されない場合
1. Cursorを再起動してください
2. 設定ファイルの構文エラーがないか確認してください

## 設定の無効化

安全な削除システムを無効にしたい場合は、`.vscode/settings.json`の以下の設定を`false`に変更してください：

```json
{
    "files.confirmDelete": false,
    "explorer.confirmDelete": false
}
```
