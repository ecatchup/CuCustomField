<?php
class CuCustomFieldUtil {
	static public function loadPlugin() {
		$path = CakePlugin::path('CuCustomField') . 'Plugin' . DS;
		App::build(['Plugin' => $path], App::PREPEND);
		$Folder = new Folder($path);
		$files = $Folder->read(true, true, false);
		$plugins = [];
		if(!empty($files[0])) {
			foreach($files[0] as $pluginName) {
				loadPlugin($pluginName, 999);
				$plugins[] = $pluginName;
			}
		}
		Configure::write('cuCustomField.plugins', $plugins);
	}
}
