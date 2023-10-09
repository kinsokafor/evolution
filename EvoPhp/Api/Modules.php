<?php 

namespace EvoPhp\Api;

use EvoPhp\Api\Config;
use Josantonius\Json\Json;

/**
 * summary
 */
class Modules
{
    /**
     * summary
     */
    public function __construct()
    {
        
    }

    static public function getModules() {
    	$config = new Config;
        if(!property_exists($config, "modules")) {
            $config->set("modules", new stdClass);
        }
        $modules = scandir("./Public/Modules");
        foreach ($modules as $module) {
            if ($module === '.' or $module === '..') continue;
            if(!isset($config->modules[$module])) {
                $file = "./Public/Modules/$module/$module.json";
                $json = new Json($file);
                if($json->exists()) {
                    $moduleConfig = $json->get();
                    $config->set("modules['$module']", $moduleConfig);
                }
            }
        }
    	return property_exists($config, "modules") ? $config->modules : [];
    }

    static public function getModulePath($module) {
        return ABSPATH."Public/Modules/$module/";
    }

    static public function moduleActive($module) {
        if(is_string($module)) return true;
        if(is_array($module) && isset($module['active'])) return $module['active'];
        return true;
    }

    static public function install() {
        $modules = self::getModules();
        if(\EvoPhp\Api\Operations::count($modules)) {
            foreach($modules as $moduleName => $module) {
                if(self::moduleActive($module)) {
                    $file = self::getModulePath($moduleName)."Install.php";
                    if(file_exists($file)) include $file;
                }
            }
        }
    }
}