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
 * @var array $definitions
 */
$formPlace = $this->request->data('CuCustomFieldConfig.form_place');
$this->BcBaser->js('https://maps.google.com/maps/api/js?key=' . $this->BcBaser->siteConfig['google_maps_api_key'], false);
$this->BcBaser->js('CuCustomField.admin/google_maps', false);
$this->BcBaser->js('CuCustomField.admin/cu_custom_field_values', false);
$this->BcBaser->css('CuCustomField.admin/cu_custom_field_values', ['inline' => false]);
echo $this->CuCustomField->BcForm->input(
	"CuCustomFieldValue.no",
	['type' => 'hidden']
);
echo $this->CuCustomField->BcForm->input(
	"CuCustomFieldValue.id",
	['type' => 'hidden']
);
?>

<?php if ($definitions): ?>

	<table class="form-table section bca-form-table" id="CuCustomFieldTable">
	<?php foreach($definitions as $keyFieldConfig => $definition): ?>
		<?php if ($this->CuCustomField->judgeStatus($definition)): ?>
			<?php
				if($definition['CuCustomFieldDefinition']['field_type'] === 'loop' && empty($definition['CuCustomFieldDefinition']['children'])){
					continue;
				}
			?>
				<tr>
					<th class="col-head bca-form-table__label">
						<?php echo $this->BcForm->label("CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}", $definition['CuCustomFieldDefinition']['name']) ?>
						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'required'])): ?>&nbsp;
							<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
						<?php endif ?>
					</th>
					<td class="col-input bca-form-table__input">
						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'prepend'])): ?>
							<div><?php echo nl2br($definition['CuCustomFieldDefinition']['prepend']) ?></div>
						<?php endif ?>

						<?php if($definition['CuCustomFieldDefinition']['field_type'] === 'loop'): ?>

							<!-- 表示 -->
							<div id="loop-<?php echo $definition['CuCustomFieldDefinition']['field_name'] ?>" class="cucf-loop">

							<?php if(!empty($this->request->data['CuCustomFieldValue'][$definition['CuCustomFieldDefinition']['field_name']]) &&
									is_array($this->request->data['CuCustomFieldValue'][$definition['CuCustomFieldDefinition']['field_name']])): ?>
								<?php foreach($this->request->data['CuCustomFieldValue'][$definition['CuCustomFieldDefinition']['field_name']] as $key => $value): ?>
								<div id="CucfLoop<?php echo $definition['CuCustomFieldDefinition']['field_name'] . '-' . $key ?>" class="cucf-loop-block">
									<table class="bca-form-table">
										<?php foreach($definition['CuCustomFieldDefinition']['children'] as $child): ?>
										<?php if ($this->CuCustomField->judgeStatus($child)): ?>
										<tr>
											<th class="bca-form-table__label">
												<?php echo $this->BcForm->label("CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}.{$key}.{$child['CuCustomFieldDefinition']['field_name']}", $child['CuCustomFieldDefinition']['name']) ?>
												<?php if ($this->CuCustomField->judgeShowFieldConfig($child, ['field' => 'required'])): ?>&nbsp;
													<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
												<?php endif ?>
											</th>
											<td class="bca-form-table__input">
												<?php echo $this->CuCustomField->input(
													"CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}.{$key}.{$child['CuCustomFieldDefinition']['field_name']}",
													$child
												) ?>
												<?php echo $this->BcForm->error("CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}_{$key}_{$child['CuCustomFieldDefinition']['field_name']}") ?>
											</td>
										</tr>
										<?php endif ?>
										<?php endforeach ?>
									</table>
									<?php echo $this->BcForm->button(__d('baser', '削除'), [
										'class' => 'btn-delete-loop bca-btn',
										'data-delete-target' => 'CucfLoop' . $definition['CuCustomFieldDefinition']['field_name'] . '-' . $key
									]) ?>
								</div>
								<?php endforeach ?>
							<?php else : ?>
								<?php $key = 0; ?>
							<?php endif ?>

							</div>

							<!-- 追加用のソース -->
							<div id="CufcLoopSrc<?php echo $definition['CuCustomFieldDefinition']['field_name'] ?>" class="cucf-loop-block" hidden>
								<table class="bca-form-table">
								<?php foreach($definition['CuCustomFieldDefinition']['children'] as $child): ?>
									<?php if ($this->CuCustomField->judgeStatus($child)): ?>
									<tr>
										<th class="bca-form-table__label">
											<?php echo $this->BcForm->label("CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}.__loop-src__.{$child['CuCustomFieldDefinition']['field_name']}", $child['CuCustomFieldDefinition']['name']) ?>
											<?php if ($this->CuCustomField->judgeShowFieldConfig($child, ['field' => 'required'])): ?>&nbsp;
												<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
											<?php endif ?>
										</th>
										<td class="bca-form-table__input">
											<?php echo $this->CuCustomField->input(
												"CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}.__loop-src__.{$child['CuCustomFieldDefinition']['field_name']}",
												$child
											) ?>
										</td>
									</tr>
									<?php endif ?>
								<?php endforeach ?>
								</table>
								<?php echo $this->BcForm->button(__d('baser', '削除'), [
									'class' => 'btn-delete-loop bca-btn',
									'data-delete-target' => 'CucfLoop' . $definition['CuCustomFieldDefinition']['field_name']
								]) ?>
							</div>

							<div class="cucf-loop-add">
								<?php echo $this->BcForm->button(__d('baser', '追加'), [
									'class' => 'bca-btn btn-add-loop',
									'id' => 'BtnAddLoop',
									'data-src' => $definition['CuCustomFieldDefinition']['field_name'],
									'data-count' => $key + 1
								]) ?>
							</div>
						<?php else: ?>

						<?php echo $this->CuCustomField->input(
							"CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}",
							$definition
						) ?>

						<?php endif ?>

						<?php echo $this->BcForm->error("CuCustomFieldValue.{$definition['CuCustomFieldDefinition']['field_name']}") ?>
						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'append'])): ?>
							<div><?php echo nl2br($definition['CuCustomFieldDefinition']['append']) ?></div>
						<?php endif ?>
						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'description'])): ?>
							<br/>
							<small><?php echo nl2br($definition['CuCustomFieldDefinition']['description']) ?></small>
						<?php endif ?>
					</td>
				</tr>
		<?php endif ?>
	<?php endforeach ?>
	</table>

<?php else: ?>

	<ul>
		<li>利用可能なフィールドがありません。不要な場合は
			<?php $this->BcBaser->link('カスタムフィールド設定',
				['plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_configs', 'action' => 'edit', $this->request->data['CuCustomFieldConfig']['id']],
				[],
				'カスタムフィールド設定画面へ移動して良いですか？編集中の内容は保存されません。'); ?>
			より無効設定ができます。
		</li>
	</ul>

<?php endif ?>
