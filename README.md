# カスタムフィールド プラグイン

PetitCustomField プラグインは、ブログ記事に入力欄を追加できるbaserCMS専用のプラグインです。

* カスタムフィールド設定管理: 利用するカスタムフィールドをコンテンツ毎（ブログ毎）に管理できます。
* カスタムフィールド管理: コンテンツ毎のカスタムフィールドの並び順、所属コンテンツの編集ができます。
* カスタムフィールド編集管理: カスタムフィールド内容を編集できます。


## Installation

1. 圧縮ファイルを解凍後、BASERCMS/app/Plugin/PetitCustomField に配置します。
2. 管理システムのプラグイン管理にアクセスし、表示されている PetitCustomField プラグイン をインストール（有効化）して下さい。
3. カスタムフィールド設定一覧画面にアクセスし、利用するコンテンツ毎に有効化します。
4. カスタムフィールド設定一覧画面の「新規追加」よりフィールドを追加します。
5. フィールド追加後、ブログ記事の投稿画面にアクセスすると、入力項目が追加されてます。


### Use Sample

公開側での利用サンプルは以下のエレメントを参照してください。

* /PetitCustomField/View/Elements/petit_blog_custom_field_block.php


## Uses Config

ブログカスタムフィールド設定画面では、ブログ別に以下の設定を行う事ができます。

* カスタムフィールドの利用の有無を選択できます。
* カスタムフィールドの表示位置を選択できます。


## CU確認済バージョン

|baserCMSバージョン|プラグインバージョン|ステータス|コメント|
|:--|:--|:--|:--|
|4.0.9|2.0.0|未承認|動作可|
|4.2.0|2.0.4|未承認|動作可|
|4.3.0|3.0.0|未承認|動作可|


## Thanks ##

- [http://basercms.net](http://basercms.net/)
- [http://wiki.basercms.net/](http://wiki.basercms.net/)
- [http://doc.basercms.net/](http://doc.basercms.net/)
- [http://cakephp.jp](http://cakephp.jp)
- [Cake Development Corporation](http://cakedc.com)
- [DerEuroMark](http://www.dereuromark.de/)
