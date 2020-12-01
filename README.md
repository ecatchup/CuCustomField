# CuCustomField

CuCustomField プラグインは、ブログ記事にオリジナルの入力欄を追加できる baserCMS 用のプラグインです。

* カスタムフィールド設定管理: 利用するカスタムフィールドをブログコンテンツごとに管理できます。
* フィールド定義管理: コンテンツごとにフィールド定義の追加、編集、削除、並び順の変更ができます。
* フィールド定義は、標準でテキスト、テキストエリア、日付（年月日）、日付（年月日時間）、チェックボックス、マルチチェックボックス、都道府県リスト、ラジオボタン、関連データ、セレクトボックス、ファイルアップロード、Google Maps、Wysiwyg Editor、ループに対応しています。

## Installation

1. 圧縮ファイルを解凍後、`/app/Plugin/CuCustomField` として配置します。
2. 管理システムのプラグイン管理にアクセスし、表示されている CuCustomField プラグイン をインストール（有効化）して下さい。


## Settings

1. プラグイン管理よりカスタムフィールド設定一覧画面にアクセスし、「新規設定追加」より、利用するブログコンテンツを選択します。
2. 「フィールド作成」ボタンよりフィールドを追加します。追加が完了するとフィールド一覧に遷移します。
3. 必要に応じてさらにフィールドを追加します。
4. その後、ブログ記事の投稿画面にアクセスすると、入力項目が追加されていることを確認します。


## Edit View

フロントエンドでカスタムフィールドのデータを読み出すには、テンプレートに関数の記述が必要です。以下のエレメント内の利用サンプルを参考にしてください。

* `/CuCustomField/View/Elements/blog_custom_field_block.php`

## Add Original Field Type

オリジナルのフィールドタイプを baserCMSのプラグインとして作成することができます。
[オリジナルのフィールドタイプを追加する](https://github.com/ecatchup/CuCustomField/blob/master/docs/CREATE_FIELD_TYPE.md) を参考にしてください。


## Thanks

- [https://basercms.net](http://basercms.net/)
- [https://wiki.basercms.net/](http://wiki.basercms.net/)
- [https://cakephp.org](https://cakephp.org)
- [Cake Development Corporation](https://cakedc.com)
- [DerEuroMark](https://www.dereuromark.de/)
