<?php
/**
 * [ADMIN] CuCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */
?>
<tr>
	<td class="row-tools">
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => '編集', 'class' => 'btn')),
			array('action' => 'edit', $data['CuCustomFieldDefinition']['id']), array('title' => '編集')) ?>
	</td>
	<td style="width: 45px;"><?php echo $data['CuCustomFieldDefinition']['id']; ?></td>
	<td>
		<?php echo $this->BcBaser->link($data['CuCustomFieldDefinition']['key'], array('action' => 'edit', $data['CuCustomFieldDefinition']['foreign_id']), array('title' => '編集')) ?>
		<?php //echo $data['CuCustomFieldDefinition']['key'] ?>
		<br />
		<?php echo $data['CuCustomFieldDefinition']['value'] ?>
	</td>
	<td style="white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['CuCustomFieldDefinition']['created']) ?>
		<br />
		<?php echo $this->BcTime->format('Y-m-d', $data['CuCustomFieldDefinition']['modified']) ?>
	</td>
</tr>
