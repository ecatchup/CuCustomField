<?php
declare(strict_types=1);

namespace CuCustomField;

use BaserCore\BcPlugin;
use BaserCore\Utility\BcEvent;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use \Cake\Core\Plugin as CakePlugin;
use Cake\Core\PluginApplicationInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\BaserCorePlugin;
use Cake\ORM\TableRegistry;
use CuCustomField\Lib\CuCustomFieldUtil;

/**
 * plugin for CuCustomField
 */
class CuCustomFieldPlugin extends BcPlugin
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function install($options = []) : bool
    {
        return parent::install($options);
    }

    public function uninstall($options = []): bool
    {
        return parent::uninstall($options);
    }

    /**
     * Bootstrap
     *
     * @param PluginApplicationInterface $app
     * @checked
     * @noTodo
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);
        $this->loadChildrenPlugin($app);
    }

    /**
     * カスタムコンテンツコアのプラグインをロードする
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function loadChildrenPlugin(PluginApplicationInterface $app): void
    {
        // プラグインの配置パスを追加
        $path = CakePlugin::path('CuCustomField') . 'plugins' . DS;
        Configure::write('App.paths.plugins', array_merge(
            Configure::read('App.paths.plugins'),
            [$path]
        ));

        $Folder = new BcFolder($path);
        $files = $Folder->getFolders();
        if (empty($files)) return;

        $collection = $pluginCollection = CakePlugin::getCollection();
        foreach($files as $pluginName) {
            // 設定ファイルを読み込む
            if (!BcUtil::includePluginClass($pluginName)){
                $plugin = $collection->create($pluginName);
                $collection->add($plugin);
                continue;
            }

            $plugin = $collection->create($pluginName);
            $collection->add($plugin);
            BcEvent::registerPluginEvent($pluginName);
        }
    }
}
