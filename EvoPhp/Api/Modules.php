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
        // if(!property_exists($config, "modules")) {
        //     $config->set("modules", new stdClass);
        // }
        $modules = scandir("./Public/Modules");
        foreach ($modules as $module) {
            if ($module === '.' or $module === '..') continue;
            if(isset($config->modules[$module]) && isset($config->modules[$module]['installed'])) {
                if($config->modules[$module]['active'] == true && $config->modules[$module]['installed'] == false) {
                    $file = self::getModulePath($module)."Install.php";
                    if(file_exists($file)) include $file;
                    $config->set("modules['$module'][installed]", true);
                }
            }
        }
    	return $config->modules ?? [];
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