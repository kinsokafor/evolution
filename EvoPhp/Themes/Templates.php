<?php 

namespace EvoPhp\Themes;
use Jenssegers\Blade\Blade;
use function array_merge, file_exists;
use EvoPhp\Api\Config;

class Templates
{
    public $data;

    function __construct()
    {
        $config = new Config;
        $this->data = (array) $config ?? [];
    }

    public function get($template, $path = './Public/Templates', $data = []) {
        $data = array_merge($this->data, $data);
        $templatePath = $path."/".$template.".blade.php";

        if(!file_exists($templatePath)) return false;
        
        $blade = new Blade($path, $path.'/cache');
        return $blade->make($template, $data)->render();
    }

    function __destruct()
    {
        
    }
}
