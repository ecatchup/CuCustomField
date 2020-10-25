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
			$allPlugins = $files[0];
			// APP 内のPlugin も対象にする
			// baser本体側でも読み込むため、二重読み込みになるが
			// bootstrap 等は置かない前提としよしとする
			$allPlugins = array_merge($allPlugins, Hash::extract(getEnablePlugins(), '{n}.Plugin.name'));
			foreach($allPlugins as $pluginName) {
				if(preg_match('/^CuCf[A-Z]/', $pluginName)) {
					loadPlugin($pluginName, 999);
					$fieldTypeSetting = Configure::read('cuCustomField.field_type');
					$fieldTypes = [];
					if ($fieldTypeSetting) {
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
			}
			Configure::write('cuCustomField.field_type', $fieldTypeAll);
			Configure::write('cuCustomField.plugins', $plugins);
			$paths = App::path('Plugin');
			array_shift($paths);
			App::build(['Plugin' => $paths], App::RESET);
		}
	}
}
