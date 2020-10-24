<?php
class CuCfFileControllerEventListener extends BcControllerEventListener {
	public $events = ['startup'];
	public function startup(CakeEvent $event) {
		/* @var CuCustomFieldValue $CuCustomFieldValue */
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$CuCustomFieldValue->Behaviors->load('CuCfFile.CuCfFile');
	}
}
