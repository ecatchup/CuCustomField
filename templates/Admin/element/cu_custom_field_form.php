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
// debug($blogContent);
$formPlace = $blogContent->cu_custom_field_config;
$this->BcBaser->js('https://maps.google.com/maps/api/js?key=' . \BaserCore\Utility\BcSiteConfig::get('google_maps_api_key'), false);
$this->BcBaser->js('CuCustomField.admin/google_maps', false);
//$this->BcBaser->js('CuCustomField.admin/jquery-2.1.4.min', false);
$this->BcBaser->js('CuCustomField.admin/cu_custom_field_values', false);
$this->BcBaser->css('CuCustomField.admin/cu_custom_field_values', false);
?>
<?php //echo $this->BcAdminForm->control('CuCustomFieldValue.no', ['type' => 'hidden']); ?>
<?php //echo $this->BcAdminForm->control('CuCustomFieldValue.relate_id', ['type' => 'hidden', 'value' => $post->id]); ?>

<?php if ($definitions): ?>

	<table class="form-table section bca-form-table" id="CuCustomFieldTable">
  	<?php foreach($definitions as $definition): ?>
  		<?php if ($this->CuCustomField->judgeStatus($definition)): ?>
  			<?php
        $fieldDefinitions = $definition->CuCustomFieldDefinitions;
        // debug($fieldDefinitions);
        // debug($post['CuCustomFieldValue'][$fieldDefinitions['field_name']]);
        // continue;
        // ループフィールドで子要素がない場合はスキップ
        if($fieldDefinitions['field_type'] === 'loop' && empty($fieldDefinitions['children'])) continue;
       ?>
  				<tr>
  					<th class="col-head bca-form-table__label">
  						<?php // ラベル（th用）
              echo $this->BcAdminForm->label("CuCustomFieldValue.{$fieldDefinitions['field_name']}", $fieldDefinitions['name']);
              ?>
  						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'required'])): ?>&nbsp;
  							<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
  						<?php endif ?>
  					</th>
  					<td class="col-input bca-form-table__input">
  						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'prepend'])): ?>
  							<div><?php echo nl2br($fieldDefinitions['prepend']) ?></div>
  						<?php endif ?>

  						<?php if($fieldDefinitions['field_type'] === 'loop'): // ループフィールドここから ?>
  							<!-- 表示 -->
  							<div id="loop-<?php echo $fieldDefinitions['field_name'] ?>" class="cucf-loop">

                 <?php
                 //debug($post->CuCustomFieldValue[$fieldDefinitions['field_name']]);
                 if(!empty($post->CuCustomFieldValue[$fieldDefinitions['field_name']]) &&
                   is_array($post->CuCustomFieldValue[$fieldDefinitions['field_name']])):
                 ?>
                 <?php /* 投稿済みデータのループ */ ?>
  								<?php foreach($post->CuCustomFieldValue[$fieldDefinitions['field_name']] as $key => $value): ?>
                    <?php $num = 0; ?>
  								<div id="CucfLoop<?php echo $fieldDefinitions['field_name'] . '-' . $key ?>" class="cucf-loop-block">
  									<table class="bca-form-table">
                      <?php /* フィールド設定のループ */ ?>
  										<?php foreach($fieldDefinitions['children'] as $child): // ループフィールド内 ?>

                        <?php $child = $child->CuCustomFieldDefinitions; ?>
    										<?php if ($this->CuCustomField->judgeStatus($child)): ?>
    										<tr>
    											<th class="bca-form-table__label">
    												<?php
                            echo $this->BcAdminForm->label("CuCustomFieldValue.{$fieldDefinitions['field_name']}.{$key}.{$child['field_name']}", $child['name']);
                            ?>
    												<?php if ($this->CuCustomField->judgeShowFieldConfig($child, ['field' => 'required'])): ?>&nbsp;
    													<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
    												<?php endif ?>
    											</th>
    											<td class="bca-form-table__input">
    												<?php
                            echo $this->CuCustomField->input(
    													"CuCustomFieldValue.{$fieldDefinitions['field_name']}.{$key}.{$child['field_name']}",
    													$child
    												);
                            ?>
    												<?php
                            echo $this->BcForm->error("CuCustomFieldValue.{$fieldDefinitions['field_name']}_{$key}_{$child['field_name']}");
                            ?>
    											</td>
    										</tr>
    										<?php endif ?>
  										<?php endforeach ?>
  									</table>
  									<?php // ループフィールドの削除
                    echo $this->BcForm->button(__d('baser', '削除'), [
  										'class' => 'btn-delete-loop bca-btn',
  										'data-delete-target' => 'CucfLoop' . $fieldDefinitions['field_name'] . '-' . $key
  									]);
                    ?>
  								</div>
  								<?php endforeach ?>
  							<?php else : ?>
  								<?php $key = 0; ?>
  							<?php endif ?>

  							</div>

  							<!-- 追加用のソース -->
  							<div id="CufcLoopSrc<?php echo $fieldDefinitions['field_name'] ?>" class="cucf-loop-block" hidden>
  								<table class="bca-form-table">
                    <?php foreach($fieldDefinitions['children'] as $child): ?>
                      <?php $child = $child->CuCustomFieldDefinitions; ?>
                      <?php if ($this->CuCustomField->judgeStatus($child)): ?>
                       <tr>
                        <th class="bca-form-table__label">
                         <?php
                         echo $this->BcAdminForm->label("CuCustomFieldValue.{$fieldDefinitions['field_name']}.__loop-src__.{$child['field_name']}", $child['name']);
                         ?>
                         <?php if ($this->CuCustomField->judgeShowFieldConfig($child, ['field' => 'required'])): ?>
                           <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
                         <?php endif ?>
                       </th>
                       <td class="bca-form-table__input">
                         <?php
                         echo $this->CuCustomField->input(
                          "CuCustomFieldValue.{$fieldDefinitions['field_name']}.__loop-src__.{$child['field_name']}",
                          $child
                        );
                        ?>
                      </td>
                    </tr>
                  <?php endif ?>
                <?php endforeach ?>
              </table>
              <?php echo $this->BcForm->button(__d('baser', '削除'), [
               'class' => 'btn-delete-loop bca-btn',
               'data-delete-target' => 'CucfLoop' . $fieldDefinitions['field_name']
             ]) ?>
           </div>

  							<div class="cucf-loop-add">
  								<?php
                  // $key に '__loop-src__' が入ってしまう問題を解決
                  if (is_int($key)) $num = $key  + 1 ;
                  $buttonOption = [
                    'class' => 'bca-btn btn-add-loop',
                    'id' => 'BtnAddLoop_'. $fieldDefinitions['field_name'],
                    'data-src' => $fieldDefinitions['field_name'],
                    'data-count' => $num
                  ];
                  echo $this->BcForm->button(__d('baser', '追加'), $buttonOption) ?>
  							</div>
  						<?php else: // ループフィールドここまで?>

    						<?php
                // //  ループフィールド以外
                // if ($fieldDefinitions['field_type'] == 'multiple') {
                //   // バックスラッシュは stripslashes() でとる
                //   $fieldValue = stripslashes($post['CuCustomFieldValue'][$fieldDefinitions['field_name']]);
                //   // unserializeする
                //   $post['CuCustomFieldValue'][$fieldDefinitions['field_name']] = unserialize($fieldValue);
                // }
                  echo $this->CuCustomField->input(
                    "CuCustomFieldValue.{$fieldDefinitions['field_name']}",
                    $fieldDefinitions
                  );
                ?>

  						<?php endif // ループフィールド以外ここまで ?>

  						<?php echo $this->BcForm->error("CuCustomFieldValue.{$fieldDefinitions['field_name']}") ?>

  						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'append'])): ?>
  							<div><?php echo nl2br($fieldDefinitions['append']) ?></div>
  						<?php endif ?>

  						<?php if ($this->CuCustomField->judgeShowFieldConfig($definition, ['field' => 'description'])): ?>
  							<br/>
  							<small><?php echo nl2br($fieldDefinitions['description']) ?></small>
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
				['controller' => 'cu_custom_field_configs', 'action' => 'edit', $this->getRequest()->getData('CuCustomFieldConfig.id')],
				[],
				'カスタムフィールド設定画面へ移動して良いですか？編集中の内容は保存されません。'); ?>
			より無効設定ができます。
		</li>
	</ul>

<?php endif ?>
