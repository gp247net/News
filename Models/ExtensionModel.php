<?php
#App\GP247\Plugins\News\Models\ExtensionModel.php
namespace App\GP247\Plugins\News\Models;

use Illuminate\Support\Facades\DB;
use GP247\Core\Models\AdminMenu;
use GP247\Front\Models\FrontLink;
use GP247\Front\Models\FrontLinkStore;
use App\GP247\Plugins\News\Models\NewsCategory;
use App\GP247\Plugins\News\Models\NewsContent;
use App\GP247\Plugins\News\Models\NewsContentDescription;

class ExtensionModel
{
    public $configGroup;
    public $configKey;
    public $requireCore;
    public $appPath;
    public function __construct()
    {
        //Read config from gp247.json
        $config = file_get_contents(__DIR__.'/../gp247.json');
        $config = json_decode($config, true);
    	$this->configGroup = $config['configGroup'];
        $this->configKey = $config['configKey'];
        $this->requireCore = $config['requireCore'];
        $this->appPath = $this->configGroup . '/' . $this->configKey;
    }

    public function uninstallExtension()
    {
        $links = FrontLink::where('module', $this->configKey)->pluck('id');
        FrontLink::where('module', $this->configKey)->delete();
        FrontLinkStore::whereIn('link_id', $links)->delete();

        (new NewsContent)->uninstall();

        //Remove menu
        (new AdminMenu)->where('uri', 'route_admin::admin_news_category.index')->delete();
        (new AdminMenu)->where('uri', 'route_admin::admin_news_content.index')->delete();
        $checkMenu = (new AdminMenu)->where('key', $this->configKey)->first();
        if ($checkMenu) {
            if (!(new AdminMenu)->where('parent_id', $checkMenu->id)->count()) {
                (new AdminMenu)->where('key', $this->configKey)->delete();
            }
        }
    }

    public function installExtension()
    {
        $link = FrontLink::create(
            [
                'name' => $this->appPath.'::'. $this->configKey . '.front.index',
                'url' => 'route_front::news.index',
                'target' => '_self',
                'module' => $this->configKey,
                'group' => 'menu',
                'status' => '1',
                'sort' => '20',
            ]
        );
        $linkId = $link->id;
        FrontLinkStore::insert(['store_id' => GP247_STORE_ID_ROOT, 'link_id' => $linkId]);
        
        $checkMenu = AdminMenu::where('key',$this->configKey)->first();
        if ($checkMenu) { 
            $position = $checkMenu->id;
        } else {
            $checkContentMenu = AdminMenu::where('key','ADMIN_CONTENT')->first();

            $checkMenu = AdminMenu::create([
                'sort' => 102,
                'parent_id' => $checkContentMenu->id ?? 0,
                'title' => $this->appPath.'::'.$this->configKey . '.news_manager',
                'icon' => 'fas fa-mug-hot',
                'key' => $this->configKey,
            ]);
            $position = $checkMenu->id;
        }
        
        (new NewsContent)->install();

        AdminMenu::insert(
            [
                'parent_id' => $position,
                'title' => $this->appPath.'::'.$this->configKey . '.news_category',
                'icon' => 'far fa-folder-open',
                'uri' => 'route_admin::admin_news_category.index',
            ]
        );
        AdminMenu::insert(
            [
                'parent_id' => $position,
                'title' => $this->appPath.'::'.$this->configKey . '.news_content',
                'icon' => 'far fa-copy',
                'uri' => 'route_admin::admin_news_content.index',
            ]
        );
    }
    
}
