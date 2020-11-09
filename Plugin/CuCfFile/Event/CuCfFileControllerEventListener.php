<?php
class CuCfFileControllerEventListener extends BcControllerEventListener {
	public $events = ['startup'];
	public function startup(CakeEvent $event) {
		if(!$this->isAction(['BlogPosts.AdminAdd', 'BlogPosts.AdminEdit'])) {
			return;
		}
		/* @var CuCustomFieldValue $CuCustomFieldValue */
		$controller = $event->subject();
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$CuCustomFieldValue->Behaviors->load('CuCfFile.CuCfFile', [
			'type' => 'BlogPost',
			'contentId' => $controller->blogContent['BlogContent']['id']
		]);
	}
}
