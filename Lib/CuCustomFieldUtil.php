<?php
class CuCustomFieldUtil {
	static public function loadPlugin() {
		$path = CakePlugin::path('CuCustomField') . 'Plugin' . DS;
		App::build(['Plugin' => $path], App::PREPEND);
		$Folder = new Folder($path);
		$files = $Folder->read(true, true, false);
		if(empty($files[0])) {
			return;
		}
		if(Configure::read('BcRequest.asset')) {
			foreach($files[0] as $pluginName) {
				CakePlugin::load($pluginName);
			}
		} else {
			$plugins = [];
			$fieldTypeAll = Configure::read('cuCustomField.field_type');
			Configure::write('cuCustomField.field_type', []);
			foreach($files[0] as $pluginName) {
				loadPlugin($pluginName, 999);
				$fieldTypeSetting = Configure::read('cuCustomField.field_type');
				$fieldTypes = [];
				if($fieldTypeSetting) {
					foreach($fieldTypeSetting as $group) {
						$fieldTypes += array_keys($group);
					}
				}
				$plugins[$pluginName] = [
					'name' => $pluginName,
					'fieldType' => $fieldTypes,
					'path' => CakePlugin::path($pluginName)
				];
				$fieldTypeAll = array_merge_recursive($fieldTypeAll, $fieldTypeSetting);
				Configure::write('cuCustomField.field_type', []);
			}
			Configure::write('cuCustomField.field_type', $fieldTypeAll);
			Configure::write('cuCustomField.plugins', $plugins);
		}
	}
}
