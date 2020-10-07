<?php
/**
 * [ADMIN] PetitCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
$this->BcBaser->css('PetitCustomField.admin/petit_custom_field', array('inline' => false));
$this->BcBaser->js(array('PetitCustomField.admin/petit_custom_field'));
$currentModelName = $this->request->params['models']['CuCustomFieldDefinition']['className'];
?>
<script type="text/javascript">
	$(window).load(function() {
		$("#CuCustomFieldDefinitionName").focus();
	});
</script>

<h3>
<?php $this->BcBaser->link($this->BcText->arrayValue($contentId, $blogContentDatas) .' ブログ設定編集はこちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents',
	'action' => 'edit', $contentId
)) ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php $this->BcBaser->link('≫記事一覧こちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts',
	'action' => 'index', $contentId
)) ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<small><?php echo $this->BcForm->input('show_field_name_list', array('type' => 'checkbox', 'label' => '利用中のフィールド名を確認する')) ?></small>
</h3>

<?php echo $this->BcForm->input('field_name_list',
	array('type' => 'select', 'multiple' => true, 'options' => $fieldNameList, 'id' => 'FieldNameList', 'class' => 'display-none')) ?>


<?php if($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('CuCustomFieldDefinition', array('url' => array('action' => 'add', $configId))) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('CuCustomFieldDefinition', array('url' => array('action' => 'edit', $configId, $foreignId))) ?>
<?php endif ?>

<div id="AjaxCheckDuplicateUrl" class="display-none">
	<?php $this->BcBaser->url(array('controller' => 'cu_custom_field_definitions', 'action' => 'ajax_check_duplicate')) ?>
</div>
<div id="ForeignId" class="display-none"><?php echo $foreignId ?></div>
<div id="CuCustomFieldDefinitionTable" class="section">
<table cellpadding="0" cellspacing="0" id="CuCustomFieldDefinitionTable1" class="form-table bca-form-table">
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('name'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.name', 'カスタムフィールド名') ?>&nbsp;<span class="required">*</span>
		</th>
		<td class="col-input bca-form-table__input" colspan="3">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.name',
					array('type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'カスタムフィールドの名称')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.name') ?>
				<div id="CheckValueResultName" class="display-none">
					<div class="error-message duplicate-error-message">同じカスタムフィールド名が存在します。変更してください。</div>
				</div>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('label_name'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.label_name', 'ラベル名') ?>&nbsp;<span class="required">*</span>
		</th>
		<td class="col-input bca-form-table__input" colspan="3">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.label_name',
					array('type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'ラベルの名称')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.label_name') ?>
				<div id="CheckValueResultLabelName" class="display-none">
					<div class="error-message duplicate-error-message">同じラベル名が存在します。変更してください。</div>
				</div>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('field_name'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.field_name', 'フィールド名') ?>&nbsp;<span class="required">*</span>
		</th>
		<td class="col-input bca-form-table__input" colspan="3">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.field_name',
					array('type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'field_name_sample')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.field_name') ?>
			<br /><small>※半角英数で入力してください。</small>
			<?php if($this->request->action == 'admin_edit'): ?>
				<span id="BeforeFieldNameComment" style="visibility: hidden;">変更前のフィールド名：</span>
				<span id="BeforeFieldName"><?php echo $this->BcForm->value('CuCustomFieldDefinition.field_name') ?></span>
			<?php endif ?>
				<div id="CheckValueResultFieldName" class="display-none">
					<div class="error-message duplicate-error-message">同じフィールド名が存在します。変更してください。</div>
				</div>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('field_type'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.field_type', 'フィールドタイプ') ?>&nbsp;<span class="required">*</span>
		</th>
		<td class="col-input bca-form-table__input" colspan="3">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.field_type', array('type' => 'select', 'options' => $customFieldConfig['field_type'])) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.field_type') ?>

			<span id="PreviewPrefList" class="display-none">
				&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->BcForm->label('preview_pref_list', '選択値対応表') ?>
				<?php echo $this->BcForm->input('preview_pref_list', array('type' => 'select', 'options' => $this->CuCustomField->previewPrefList())) ?>
			</span>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('status'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.status', '利用状態') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.status', array('type' => 'checkbox', 'label' => '利用中')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.status') ?>
		</td>
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.required', '必須設定') ?>
		</th>
		<td class="col-input bca-form-table__input" id="Row<?php echo $currentModelName . Inflector::camelize('required'); ?>">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.required', array('type' => 'checkbox', 'label' => '必須入力とする')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.required') ?>
		</td>
	</tr>
</table>


<h3>管理システム表示設定</h3>
<table cellpadding="0" cellspacing="0" id="CuCustomFieldDefinitionTable2" class="form-table bca-form-table">
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('default_value'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.default_value', '初期値') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.default_value', array('type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true)) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionDefaultValue', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionDefaultValue" class="helptext">
					<h5 class="weight-bold">ラジオボタン、セレクトボックスの場合</h5>
					<ul>
						<li>選択肢の入力内容のラベル名（キー）を指定してください。</li>
						<li>選択肢でラベル名（キー）と値を指定した場合は、値を指定してください。</li>
					</ul>
					<h5 class="weight-bold">チェックボックスの場合</h5>
					<ul>
						<li>「1」を指定すると、初期表示はチェックが入った状態になります。</li>
					</ul>
					<h5 class="weight-bold">マルチチェックボックスの場合</h5>
					<ul>
						<li>半角パイプ「|」で値を区切ると、初期表示は複数にチェックが入った状態になります。</li>
					</ul>
					<h5 class="weight-bold">都道府県リストの場合</h5>
					<ul>
						<li>選択値対応表の「値」を初期値として指定できます。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.default_value') ?>
			<br /><small>※入力欄の初期値を指定できます。</small>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('validate'); ?>Group">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate', '入力値チェック') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.validate', array('type' => 'select', 'multiple' => 'checkbox', 'options' => $customFieldConfig['validate'])) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate') ?>

			<div id="CuCustomFieldDefinitionValidateRegexBox" class="display-none" style="clear: both;">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate_regex', '正規表現入力') ?>&nbsp;<span class="required">*</span>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.validate_regex',
					array('type' => 'text', 'size' => 45, 'maxlength' => 255, 'placeholder' => '例：/^[a-z]+$/i')) ?>
					<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionValidateRegex', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<div id="helptextCuCustomFieldDefinitionValidateRegex" class="helptext">
						<ul>
							<li>正規表現（preg_match）を用いて入力データのチェックができます。/〜/ の形式で入力してください。</li>
							<li>ご入力の正規表現自体の正誤チェックは行いません。</li>
							<li>「エラー用文言」入力欄では、正規表現チェック時のエラーメッセージを指定できます。</li>
							<li>エラーメッセージの指定がない場合は「入力エラーが発生しました。」となります。</li>
						</ul>
					</div>
				<span id="CheckValueResultValidateRegex" class="display-none">
					<div class="error-message duplicate-error-message">正規表現を入力してください。</div>
				</span>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate_regex') ?>
				<br />
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate_regex_message', 'エラー用文言') ?>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.validate_regex_message',
					array('type' => 'text', 'size' => 49, 'maxlength' => 255, 'placeholder' => '入力エラーが発生しました。')) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate_regex_message') ?>
			</div>
		</td>
	</tr>
	<tr  id="Row<?php echo $currentModelName . Inflector::camelize('size'); ?>Group">
		<th class="col-head bca-form-table__label">
			テキスト
		</th>
		<td class="col-input bca-form-table__input">
			<div class="pcf-input-box">
				<span class="span4" id="Row<?php echo $currentModelName . Inflector::camelize('size'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.size', '入力サイズ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.size', array('type' => 'text', 'size' => 4, 'placeholder' => '60')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.size') ?>
				</span>
				<span class="span4" id="Row<?php echo $currentModelName . Inflector::camelize('max_lenght'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.max_length', '最大入力文字数') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.max_length', array('type' => 'text', 'size' => 4, 'placeholder' => '255')) ?>
					<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionMaxLength', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<div id="helptextCuCustomFieldDefinitionMaxLength" class="helptext">
						<ul>
							<li>入力すると、指定文字数制限による入力チェックが行われます。</li>
						</ul>
					</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.max_length') ?>
				</span>
				<span class="span4" id="Row<?php echo $currentModelName . Inflector::camelize('counter'); ?>">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.counter', array('type' => 'checkbox', 'label' => '文字数カウンターを表示する')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.counter') ?>
				</span>
			</div>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('placeholder'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.placeholder', 'プレースホルダー') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.placeholder', array('type' => 'text', 'size' => 60, 'placeholder' => 'プレースホルダー表示例')) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionPlaceholder', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionPlaceholder" class="helptext">
					<ul>
						<li>入力欄内に未入力時に表示される文字を指定できます。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.placeholder') ?>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('rows'); ?>Group">
		<th class="col-head bca-form-table__label">
			テキストエリア
		</th>
		<td class="col-input bca-form-table__input">
			<div class="pcf-input-box">
				<span class="span4" id="Row<?php echo $currentModelName . Inflector::camelize('rows'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.rows', '行数') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.rows', array('type' => 'text', 'size' => 5, 'placeholder' => '3')) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionRows', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionRows" class="helptext">
					<ul>
						<li>テキストエリアの場合は行数指定となります。</li>
						<li>Wysiwyg Editorの場合はpx指定となります。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.rows') ?>
				</span>
				<span class="span4"id="Row<?php echo $currentModelName . Inflector::camelize('cols'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.cols', '横幅サイズ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.cols', array('type' => 'text', 'size' => 5, 'placeholder' => '40')) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionCols', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionCols" class="helptext">
					<ul>
						<li>テキストエリアの場合は数値指定となります。</li>
						<li>Wysiwyg Editorの場合は％指定となります。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.cols') ?>
				</span>
				<span class="span4" id="Row<?php echo $currentModelName . Inflector::camelize('editor_tool_type'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.editor_tool_type', 'Ckeditorのタイプ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.editor_tool_type', array('type' => 'select', 'options' => $customFieldConfig['editor_tool_type'])) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.editor_tool_type') ?>
				</span>
			</div>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('choices'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.choices', '選択肢') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.choices', array('type' => 'textarea', 'rows' => '4')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.choices') ?>
			<br /><small>選択肢を改行毎に入力します。</small>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionChoices', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionChoices" class="helptext">
					<ul>
						<li>より細かく制御する場合は、ラベル名（キー）と値の両方を指定することができます。</li>
						<li>指定したいラベル名（キー）と値を半角「:」で区切って入力してください。</li>
						<li>（例：ラベル名:値）</li>
					</ul>
				</div>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('separator'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.separator', '区切り文字') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.separator', array('type' => 'text', 'size' => 60, 'placeholder' => '&nbsp;&nbsp;')) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.separator') ?>
			<br /><small>※ラジオボタン表示の際の区切り文字を指定できます。</small>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('auto_convert'); ?>">
		<th class="col-head bca-form-table__label">
			入力テキスト変換処理
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.auto_convert', '自動変換') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.auto_convert', array('type' => 'select', 'options' => $customFieldConfig['auto_convert'])) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionAutoConvert', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionAutoConvert" class="helptext">
					<ul>
						<li>半角変換を指定すると、「全角」英数字を「半角」に変換して保存します。</li>
						<li>フィールドタイプがテキスト、テキストエリアの際に変換処理は実行されます。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.auto_convert') ?>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('google_maps'); ?>Group">
		<th class="col-head bca-form-table__label">
			初期値
		</th>
		<td class="col-input bca-form-table__input">

			<div class="pcf-input-box googlemaps-input-box">
				<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_latitude'); ?>">
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_latitude', '緯度') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_latitude', array('type' => 'text', 'size' => 22)) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_latitude') ?>
				</span>
				<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_longtude'); ?>">
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_longtude', '経度') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_longtude', array('type' => 'text', 'size' => 22)) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_longtude') ?>
				</span>
				<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_zoom'); ?>">
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_zoom', 'ズーム値') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_zoom', array('type' => 'text', 'size' => 4)) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_zoom') ?>
				</span>
				<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_text'); ?>">
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_text', 'ポップアップテキスト') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_text', array('type' => 'text', 'size' => 60)) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_text') ?>
				</span>
			</div>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('prepend'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.prepend', '入力欄前に表示') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.prepend', array('type' => 'text', 'size' => 60)) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionPrepend', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionPrepend" class="helptext">
					<ul>
						<li>入力欄の前に表示される文字を指定できます。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.prepend') ?>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('append'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.append', '入力欄後に表示') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.append', array('type' => 'text', 'size' => 60)) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionAppend', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionAppend" class="helptext">
					<ul>
						<li>入力欄の後に表示される文字を指定できます。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.append') ?>
		</td>
	</tr>
	<tr id="Row<?php echo $currentModelName . Inflector::camelize('description'); ?>">
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.description', 'このフィールドの説明文') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.description', array('type' => 'textarea', 'rows' => '2')) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldDefinitionDescription', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldDefinitionDescription" class="helptext">
					<ul>
						<li>入力欄に説明文を指定できます。</li>
						<li>入力内容は、編集欄下部に表示されます。</li>
					</ul>
				</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.description') ?>
		</td>
	</tr>
</table>
</div>

<?php if ($this->BcBaser->siteConfig['admin_theme'] == 'admin-third'):?>
	<!-- button -->
	<div class="submit bca-actions">
		<div class="bca-actions__main">
			<?php
			echo $this->BcForm->button(__d('baser', '保存'),
				[
					'div' => false,
					'class' => 'button bca-btn bca-actions__item',
					'data-bca-btn-type' => 'save',
					'data-bca-btn-size' => 'lg',
					'data-bca-btn-width' => 'lg',
				]);
			?>
		</div>
		<?php if ($this->action == 'admin_edit'): ?>
			<div class="bca-actions__sub">
				<?php
				$this->BcBaser->link(__d('baser', '削除'),
					[
						'action' => 'delete',
						$configId,
						$foreignId
					],
					[
						'class' => 'submit-token button bca-btn bca-actions__item',
						'data-bca-btn-type' => 'delete',
						'data-bca-btn-size' => 'sm'
					],
					sprintf('ID：%s のデータを削除して良いですか？', $this->BcForm->value('CuCustomFieldDefinition.name')),
					false
				);
				?>
			</div>
		<?php endif ?>
	</div>
<?php else: ?>
	<div class="submit">
		<?php echo $this->BcForm->submit('保　存', array('div' => false, 'class' => 'button btn-red', 'id' => 'BtnSave')) ?>
		<?php if ($deletable): ?>
			<?php $this->BcBaser->link('削　除',
				array('action' => 'delete', $configId, $foreignId),
				array('class' => 'btn-gray button', 'id' => 'BtnDelete'),
				sprintf('ID：%s のデータを削除して良いですか？', $this->BcForm->value('CuCustomFieldDefinition.name')),
				false); ?>
		<?php endif ?>
	</div>
<?php endif ?>

<?php echo $this->BcForm->end() ?>
<?php
if(Configure::read('cuCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>
