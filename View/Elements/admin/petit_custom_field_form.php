<?php
/**
 * [ADMIN] PetitCustomField
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			PetitCustomField
 * @license			MIT
 */
$formPlace = $this->request->data('PetitCustomFieldConfig.form_place');
?>
<?php if ($formPlace !== 'top'): ?></table><?php endif ?>

<h3 id="textPetitCustomFieldTable">カスタム項目</h3>
<?php if ($fieldConfigField): ?>
<table cellpadding="0" cellspacing="0" class="form-table section" id="PetitCustomFieldTable">
	<?php foreach ($fieldConfigField as $keyFieldConfig => $valueFieldConfig): ?>

		<?php if ($this->PetitCustomField->judgeStatus($valueFieldConfig)): ?>
			<?php if ($valueFieldConfig['PetitCustomFieldConfigField']['field_type'] == 'googlemaps'): ?>
				<tr>
					<th colspan="2">
						<?php echo $this->BcForm->label("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}", $valueFieldConfig['PetitCustomFieldConfigField']['name']) ?>
					</th>
				</tr>
				<tr class="petit-google-maps-form">
					<td class="col-input" colspan="2">
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'prepend'))): ?>
							<div><?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['prepend']) ?></div>
						<?php endif ?>

						<?php if (!empty($this->BcBaser->siteConfig['google_maps_api_key'])): ?>
							<div class="petit-google-maps" style="width:100%; height:450px;"></div>
							<?php echo $this->BcBaser->js('https://maps.google.com/maps/api/js?key=' . $this->BcBaser->siteConfig['google_maps_api_key']) ?>
							<?php echo $this->BcBaser->js('PetitCustomField.admin/google_maps') ?>

							<div style="margin-right: 5px;">
								<?php echo $this->BcForm->input('google_maps_address',
									array('type' => 'text', 'name' => '', 'class' => 'petit-google_maps_address')) ?>
								<?php echo $this->BcForm->button('入力住所から地図を設定', array('type' => 'button', 'class' => 'petit-set_google_maps_setting', 'size' => 40)) ?>
							</div>

							<?php echo '緯度' . $this->PetitCustomField->input("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}.google_maps_latitude", array('type' => 'text', 'class' => 'petit-google_maps_latitude', 'default' => $valueFieldConfig['PetitCustomFieldConfigField']['google_maps_latitude'], 'size' => 22)); ?>

							<?php echo '経度' . $this->PetitCustomField->input("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}.google_maps_longtude", array('type' => 'text', 'class' => 'petit-google_maps_longtude', 'default' => $valueFieldConfig['PetitCustomFieldConfigField']['google_maps_longtude'], 'size' => 22)); ?>

							<?php echo 'ズーム値' . $this->PetitCustomField->input("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}.google_maps_zoom", array('type' => 'text', 'class' => 'petit-google_maps_zoom', 'default' => $valueFieldConfig['PetitCustomFieldConfigField']['google_maps_zoom'], 'size' => 4)); ?>

							<br>

							<?php echo 'ポップアップテキスト' . $this->PetitCustomField->input("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}.google_maps_text", array('type' => 'text', 'class' => 'petit-google_maps_text', 'default' => $valueFieldConfig['PetitCustomFieldConfigField']['google_maps_text'], 'size' => 60)); ?>
						<?php else: ?>
							※Googleマップを利用するには、Google Maps APIのキーの登録が必要です。キーを取得して、システム管理より設定してください。
						<?php endif; ?>

						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'append'))): ?>
							<div><?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['append']) ?></div>
						<?php endif ?>
					</td>
				</tr>
			<?php elseif ($valueFieldConfig['PetitCustomFieldConfigField']['field_type'] == 'wysiwyg'): ?>
				<?php // Wysiwyg の場合 ?>
				<tr>
					<th colspan="2">
						<?php echo $this->BcForm->label("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}", $valueFieldConfig['PetitCustomFieldConfigField']['name']) ?>
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'required'))): ?>&nbsp;<span class="required">*</span><?php endif ?>
					</th>
				</tr>
				<tr>
					<td class="col-input" colspan="2">
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'prepend'))): ?>
							<?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['prepend']) ?>
						<?php endif ?>
						
						<?php echo $this->PetitCustomField->input("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}",
							$this->PetitCustomField->getFormOption($valueFieldConfig, 'PetitCustomFieldConfigField')
						) ?>
						
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'append'))): ?>
							<?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['append']) ?>
						<?php endif ?>
						
						<?php echo $this->BcForm->error("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}") ?>
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'description'))): ?>
							<br /><small><?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['description']) ?></small>
						<?php endif ?>
					</td>
				</tr>
			<?php elseif ($valueFieldConfig['PetitCustomFieldConfigField']['field_type'] == 'upload'): ?>
				<?php // アップロードの場合 ?>
				<tr>
					<th class="col-head">
						<?php echo $this->BcForm->label("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}", $valueFieldConfig['PetitCustomFieldConfigField']['name']) ?>
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'required'))): ?>&nbsp;<span class="required">*</span><?php endif ?>
					</th>
					<td class="col-input">
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'prepend'))): ?>
							<?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['prepend']) ?>
						<?php endif ?>
						
						<?php echo $this->PetitCustomField->input("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}",
							$this->PetitCustomField->getFormOption($valueFieldConfig, 'PetitCustomFieldConfigField')
						) ?>
						
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'append'))): ?>
							<?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['append']) ?>
						<?php endif ?>
						
						<?php echo $this->BcForm->error("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}") ?>
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'description'))): ?>
							<br /><small><?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['description']) ?></small>
						<?php endif ?>
					</td>
				</tr>
			<?php else: ?>
				<?php // デフォルトのフィールド ?>
				<tr>
					<th class="col-head">
						<?php echo $this->BcForm->label("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}", $valueFieldConfig['PetitCustomFieldConfigField']['name']) ?>
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'required'))): ?>&nbsp;<span class="required">*</span><?php endif ?>
					</th>
					<td class="col-input">
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'prepend'))): ?>
							<?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['prepend']) ?>
						<?php endif ?>
						
						<?php echo $this->PetitCustomField->input("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}",
							$this->PetitCustomField->getFormOption($valueFieldConfig, 'PetitCustomFieldConfigField')
						) ?>

						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'append'))): ?>
							<?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['append']) ?>
						<?php endif ?>
						
						<?php echo $this->BcForm->error("PetitCustomField.{$valueFieldConfig['PetitCustomFieldConfigField']['field_name']}") ?>
						<?php if ($this->PetitCustomField->judgeShowFieldConfig($valueFieldConfig, array('field' => 'description'))): ?>
							<br /><small><?php echo nl2br($valueFieldConfig['PetitCustomFieldConfigField']['description']) ?></small>
						<?php endif ?>
					</td>
				</tr>
			<?php endif ?>
		<?php endif ?>
	
	<?php endforeach ?>
<?php if ($formPlace !== 'normal'): ?></table><?php endif ?>
<?php else: ?>
<ul>
	<li>利用可能なフィールドがありません。不要な場合は
		<?php $this->BcBaser->link('カスタムフィールド設定',
			array('plugin' => 'petit_custom_field', 'controller' => 'petit_custom_field_configs', 'action'=>'edit', $this->request->data['PetitCustomFieldConfig']['id']),
			array(),
			'カスタムフィールド設定画面へ移動して良いですか？編集中の内容は保存されません。',
			false); ?>
		より無効設定ができます。		
	</li>
</ul>
<?php endif ?>
