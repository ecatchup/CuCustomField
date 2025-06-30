<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.View
 * @license          MIT LICENSE
 */

/**
 * @var BcAppView $this
 */
?>


<?php echo $this->BcForm->create('CuCustomFieldConfig', array('action' => 'first')) ?>
<?php echo $this->BcForm->input('CuCustomFieldConfig.active', array('type' => 'hidden', 'value' => '1')) ?>
<table cellpadding="0" cellspacing="0" class="form-table section" id="ListTable">
	<tr>
		<th class="col-head">
			はじめに<br />お読み下さい。
		</th>
		<td class="col-input">
			<strong>カスタムフィールド設定データ作成では、各ブログ用のカスタムフィールド設定データを作成します。</strong>
			<ul>
				<li>カスタムフィールド設定データがないブログ用のデータのみ作成します。</li>
			</ul>
		</td>
	</tr>
</table>

<div class="submit">
	<?php echo $this->BcForm->submit('作成する', array(
		'div' => false,
		'class' => 'btn-red button',
		'id' => 'BtnSubmit',
		'onClick'=>"return confirm('カスタムフィールド設定データの作成を行いますが良いですか？')")) ?>
</div>
<?php echo $this->BcForm->end() ?>
