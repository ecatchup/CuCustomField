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


<tr id="RowCuCfGoogleMaps">
	<th class="bca-form-table__label">
		初期値
	</th>
	<td class="bca-form-table__input">
		<div class="googlemaps-input-box">
			<p>
				<span>
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_latitude', '緯度') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_latitude', ['type' => 'text', 'size' => 22]) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_latitude') ?>
				</span>
				<span>
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_longitude', '経度') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_longitude', ['type' => 'text', 'size' => 22]) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_longitude') ?>
				</span>
				<span>
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_zoom', 'ズーム値') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_zoom', ['type' => 'text', 'size' => 4]) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_zoom') ?>
				</span>
			</p>
			<p>
				<span>
					<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_text', 'ポップアップテキスト') ?>
					<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_text', ['type' => 'text', 'size' => 60]) ?>
					<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_text') ?>
				</span>
			</p>
		</div>
	</td>
</tr>
