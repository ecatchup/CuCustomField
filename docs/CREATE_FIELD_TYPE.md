# オリジナルのフィールドタイプを作成する

`CuCustomField` のオリジナルのフィールドタイプは、いくつかのファイルを準備するだけで baserCMSのプラグインとして作成することができます。

## プラグインフォルダ名

先頭に `CuCf` というプレフィクスをつけた名称とすることで、`CuCustomField` のプラグインとして認識されます。

例） `CuCfCatchup`

## 設定ファイルの準備

フィールドタイプの名称を定義するための設定ファイルを準備します。分類として、基本、選択、コンテンツ、その他の中に、複数のフィールドタイプを定義することができます。

`/Config/setting.php`

```php
$config['cuCustomField'] = [
	'field_type' => [
		'その他' => [
			'catchup' => 'キャッチアップ'
]]];
```

## フィールド定義の入力欄の準備

`CuCustomField` が保有する入力欄だけを利用する場合は必要ありませんが、独自の入力欄をフィールド定義に追加するには、次のファイルを配置します。

`/View/Elements/admin/definition_input.php`

このファイルに入力欄の定義を記述します。

```php
<tr id="RowCuCfCatchup">
    <th class="bca-form-table__label">
        <?php echo $this->BcForm->label('CuCustomFieldDefinition.catchup', 'キャッチアップの設定') ?>
    </th>
    <td class="bca-form-table__input">
        <?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.catchup', [
            'type' => 'text',
            'size' => 60
        ]) ?>
        <?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.catchup') ?>
    </td>
</tr>
```

`input` メソッドのフィールド名は、`CuCustomFieldDefinition.option_meta.catchup` のように、option_meta の配下として定義することでデータベース構造の変更が不要となります。

## フィールド定義の入力欄操作のための Javascript を準備

入力欄において利用する入力欄を設定したり利用しない入力欄を非表示にするため、制御用の Javascript を配置します。

`/webroot/js/admin/definition_input.js`

```js
$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");
    fieldType.change(switchRelated);
    switchRelated();
    function switchRelated() {
        if(fieldType.val() === 'catchup') {
            $("#RowCuCfCatchup").show();
        } else {
            $("#RowCuCfCatchup").hide();
        }
    }
});
```

## ヘルパの準備

入力欄の定義とデータ取得機能を実装するためにヘルパを準備します。
クラス名はフィールドタイプ名に連携するよう CuCf{フィールドタイプ名}Helper とします。
複数のフィールドタイプを作成する場合は、複数のヘルパを作成する必要があります。

例）`CuCfCatchupHelper`

### 記事編集画面での入力欄の定義

入力欄の定義のため、 `input` メソッドを実装します。

作成したヘルパは、`CuCustomFieldHelper` をプロパティとして持つ仕様となっていますので、そのヘルパがさらにプロパティとして設定している、`BcFormHelper` を利用して入力欄を作成するための記述を行います。

```php
public function input ($fieldName, $definition, $options) {
    $options = array_merge([
        'type' => 'select',
    ], $options);
    return $this->CuCustomField->BcForm->input($fieldName, $options);
}
```

引数の `$definition` には、フィールド定義で登録したデータが渡ってきますので必要に応じて利用します。

### 記事表示画面でのデータ取得機能の定義

取得について `get` メソッドを実装します。

```php
public function get($fieldValue, $definition, $options) {
    return h($fieldValue);
}
```

### プラグインを有効化する

管理画面のプラグイン管理より作成したプラグインをインストールすることで実際に利用できるようになります。

### 既存のプラグインを参考にする

`CuCustomField` 内の `/Plugin/` に、既存のプラグインが存在しますので参考にしてみてください。

