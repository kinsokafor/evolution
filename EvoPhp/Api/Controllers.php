<?php 

namespace EvoPhp\Api;

use Jenssegers\Blade\Blade;
use EvoPhp\Api\Operations;
use EvoPhp\Themes\Themes;
use EvoPhp\Api\Config;
use Assoto\JS;
use Assoto\CSS;
use KubAT\PhpSimple\HtmlDomParser;
use EvoPhp\Database\Session;

/**
 * summary
 */
abstract class Controllers
{
    use Auth;
    /**
     * summary
     */
    public $bladeTemplate;

    public $viewPath;

    public $config;

    public $dataMethod;

    public $resourceMethod;

    public array $data = [];

    protected string $template = "";

    private array $reservedMethods = [
        "auth",
        "template",
        "setData"
    ];

    public function __construct()
    {
        $this->config = new Config;
        $this->data = (array) $this->config->data ?? [];
    }

    public function __call($name, $args) {
        if(in_array($name, $this->reservedMethods)) :
            $this->$name(...$args);
            return $this;
        endif;
        $this->bladeTemplate = $name;
        if(isset($args[0])) {
        	$this->data = array_merge($this->data, $args[0]);
        }
        $this->dataMethod = str_replace("/", "__", $this->bladeTemplate."Data");
        $this->resourceMethod = str_replace("/", "__", $this->bladeTemplate."Resources");
        $data = $this->getData($this->data);
        $this->data['nonce'] = $this->getNonce();
        if($data && is_array($data)) {
            $this->data = array_merge($this->data, $data);
        }
        return $this;
    }

    protected function setData($data) {
        if(Operations::count($data)) {
            $this->data = array_merge($this->data, $data);
        }
    }

    protected function auth(...$accessLevel) {
        $this->accessLevel = $accessLevel;
        $this->accessType = Operations::count($accessLevel) ? "protected" : "public";
    }

    protected function template(...$template) {
        $this->template = $template[0] ?? "";
    }

    private function getContent() {
        $this->addResources();
        $controllerData = get_object_vars($this);
        $controllerData = array_merge($controllerData, ($controllerData["data"] ?? []));
        unset($controllerData["data"]);
        $this->data = array_merge($this->data, $controllerData);
        if($this->template == 'blank') return "";
        $blade = new Blade($this->viewPath, $this->viewPath.'/cache');
        $themes = new Themes($this->template, $this->data);
        $content = $blade->make($this->bladeTemplate, $themes->data)->render();
        if($this->template == 'no_theme') return $content;
        return $themes->getView($content);
    }

    private function addBundles() {
        $file = "./Public/dist/$this->bladeTemplate.html";
        if(!@file_exists($file)) return;
        $dom = HtmlDomParser::file_get_html( $file );
        try {
            $styles = $dom->find('link');
            if(Operations::count($styles)) {
                foreach ($styles as $key => $style) {
                    // if(!strstr($style->href, "/".$this->bladeTemplate."__")) continue;
                    CSS::stylesheet("/Public/dist".$style->href, '', ["defer" => "defer", "rel" => "preload"]);
                }
            }
            $scripts = $dom->find('script');
            if(Operations::count($scripts)) {
                foreach ($scripts as $key => $script) {
                    // if(!strstr($script->src, "/".$this->bladeTemplate."__")) continue;
                    JS::file("/Public/dist".$script->src, '', ["defer" => "defer", "rel" => "preload"]);
                }
            }
        } catch (\Throwable $e) {
            // Handle exceptions
            error_log("Caught Exception: " . $e->getMessage());
            echo "Something went wrong. " . $e->getMessage();
        }
    }

    public function __destruct() {
        $session = Session::getInstance();
        $this->setData(["resourceOwner" => $session->getResourceOwner()]);
        if($this->accessType == "public" || $this->getAuthorization(true)) {
            $this->addBundles();
            echo $this->getContent();
            return;
        }
        self::signOut();
        $config = new Config;
        $home = isset($config->links) ? $config->links['home'] ?? "/accounts" : $config->loginLink;
        header("HTTP/1.1 401 Unauthorized");
        header("Location: $home");
    }

    abstract function getData($data);

    abstract function addResources();
}