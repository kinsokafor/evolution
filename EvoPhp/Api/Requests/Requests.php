<?php 

namespace EvoPhp\Api\Requests;

use EvoPhp\Api\Operations;
use function getallheaders;
use EvoPhp\Api\FileHandling\Files;
// use EvoPhp\Resources\Post;
// use EvoPhp\Resources\User;
// use EvoPhp\Resources\Options;
// use EvoPhp\Resources\Records;
use EvoPhp\Api\Config;

class Requests
{
    use \EvoPhp\Api\Auth;

	public $response;

    public $requestHeaders;

    public $joinUserAt;

    public $joinPostAt;

    public $joinStoreAt;

    public $joinAt;

    public $joinTable;

    public $order_by = false;

    public $order = false;

    public string $tableName;

    public bool $isCount = false;

    public int | bool $limit = false;

    public int | bool $offset = false;

    public bool $cache = true;

    public int $cacheExpiry = 300;

    public array $data = [];

    public array $uniqueKeys = [];

    public array $rightColumns = [];

    public $protocol;

    protected $verified = false;

    public array $reservedMethods = [
        "auth",
        "setData",
        "execute"
    ];

	public function __construct()
    {
        $this->data = (array) json_decode(file_get_contents('php://input'));
        $this->setRequestHeaders();
        $this->setResponseHeaders();
    }

    public function __call($name, $args) {
        if(in_array($name, $this->reservedMethods)) :
            $this->$name(...$args);
            return $this;
        endif;
        $this->tableName = $name;
        if($this->requestHeaders->requestMethod == 'get' && Operations::count($_GET)) {
            $this->data = array_merge($this->data, array_map(array($this, 'uriDecode'), $_GET));
        }
        else if($this->requestHeaders->requestMethod == 'delete' && Operations::count($_GET)) {
            $this->data = array_merge($this->data, array_map(array($this, 'uriDecode'), $_GET));
        }
        else if($this->requestHeaders->requestMethod == 'post' && Operations::count($_POST)) {
            $this->data = array_merge($this->data, array_map(array($this, 'uriDecode'), (array) json_decode(file_get_contents("php://input"), true)));
        }
        if(isset($args[0])) {
        	$this->data = array_merge($this->data, $args[0]);
        }
        return $this;
    }

    protected function setData($data) {
        if(Operations::count($data)) {
            $this->data = array_merge($this->data, array_map(array($this, 'uriDecode'), $data));
        }
    }

    protected function uriDecode($value) {
        return rawurldecode($value);
    }

    protected function auth(...$accessLevel) {
        $this->accessLevel = $accessLevel;
        $this->accessType = Operations::count($accessLevel) ? "protected" : "public";
    }

    private function execute($callable) {
        if($this->verifyClient()) {
            $this->processFiles($this->tableName);
            $this->data['response'] = $callable($this->data);
        }
    }

    private function get() {                    
        if(isset($this->data['limit'])) {
            $this->limit = (int) $this->data['limit'];
            unset($this->data['limit']);
        }
        if(isset($this->data['offset'])) {
            $this->offset = (int) $this->data['offset'];
            unset($this->data['offset']);
        }
        if(isset($this->data['order_by'])) {
            $this->order_by = $this->data['order_by'];
            unset($this->data['order_by']);
        }
        if(isset($this->data['order'])) {
            $this->order = $this->data['order'];
            unset($this->data['order']);
        }
        if(isset($this->data['iscount'])) {
            $this->isCount = true;
            unset($this->data['iscount']);
        }
        if(isset($this->data['rightcolumns'])) {
            $this->rightColumns = explode(",", $this->data['rightcolumns']);
            unset($this->data['rightcolumns']);
        }
        if(isset($this->data['joinuserat'])) {
            $this->joinUserAt = $this->data['joinuserat'];
            unset($this->data['joinuserat']);
        }
        if(isset($this->data['joinpostat'])) {
            $this->joinPostAt = $this->data['joinpostat'];
            unset($this->data['joinpostat']);
        }
        if(isset($this->data['joinstoreat'])) {
            $this->joinStoreAt = $this->data['joinstoreat'];
            unset($this->data['joinstoreat']);
        }
        if(isset($this->data['joinat']) && isset($this->data['jointable'])) {
            $this->joinAt = $this->data['joinat'];
            $this->joinTable = $this->data['jointable'];
            unset($this->data['joinat']);
            unset($this->data['jointable']);
        }
        switch ($this->tableName) {
            case 'post':
                GetRequest::postTable($this);
                break;

            case 'user':
                GetRequest::usersTable($this);
                break;

            case 'options':
                GetRequest::optionsTable($this);
                break;

            case 'store':
                GetRequest::storeTable($this);
                break;

            case 'records':
                GetRequest::recordsTable($this);
                break;
            
            default:
                GetRequest::dbTable($this);
                break;
        }
    }

    private function delete() {                        
        if(isset($this->data['limit'])) {
            $this->limit = (int) $this->data['limit'];
            unset($this->data['limit']);
        }
        if(isset($this->data['offset'])) {
            $this->offset = (int) $this->data['offset'];
            unset($this->data['offset']);
        }
        switch ($this->tableName) {
            case 'post':
                DeleteRequest::postTable($this);
                break;

            case 'user':
                DeleteRequest::usersTable($this);
                break;

            case 'store':
                DeleteRequest::storeTable($this);
                break;

            case 'options':
                DeleteRequest::optionsTable($this);
                break;

            case 'records':
                DeleteRequest::recordsTable($this);
                break;
            
            default:
                DeleteRequest::dbTable($this);
                break;
        }
    }

    private function post() {
        switch ($this->tableName) {
            case 'post':
                $this->processFiles("post/");
                PostRequest::postTable($this);
                break;

            case 'user':
                $this->processFiles("user/");
                PostRequest::usersTable($this);
                break;

            case 'options':
                $this->processFiles("options/");
                PostRequest::optionsTable($this);
                break;

            case 'store':
                $this->processFiles("store/");
                PostRequest::storeTable($this);
                break;

            case 'records':
                $this->processFiles("records/");
                PostRequest::recordsTable($this);
                break;

            case 'evoAction':
            case 'evoActions':
                PostRequest::evoActions($this);
                break;
            
            default:
                PostRequest::dbTable($this);
                break;
        }
    }

    private function put() {
        switch ($this->tableName) {
            case 'post':
                $this->processFiles("post/");
                PutRequest::postTable($this);
                break;

            case 'user':
                $this->processFiles("user/");
                PutRequest::usersTable($this);
                break;

            case 'store':
                $this->processFiles("store/");
                PutRequest::storeTable($this);
                break;

            case 'options':
                $this->processFiles("options/");
                PutRequest::optionsTable($this);
                break;

            case 'records':
                $this->processFiles("records/");
                PutRequest::recordsTable($this);
                break;

            case 'evoAction':
            case 'evoActions':
                PostRequest::evoActions($this);
                break;
            
            default:
                PutRequest::dbTable($this);
                break;
        }
    }

    public function setUniqueKeys() {
        if(isset($this->data['uniqueKeys'])) {
            switch (gettype($this->data['uniqueKeys'])) {
                case 'string':
                    $this->uniqueKeys = explode(",", $this->data['uniqueKeys']);
                    break;
                
                case 'array':
                    $this->uniqueKeys = $this->data['uniqueKeys'];
                    break;

                case 'object':
                    $this->uniqueKeys = (array) $this->data['uniqueKeys'];
                    $this->uniqueKeys = array_values($this->uniqueKeys);
                    break;
                
                default:
                    # code...
                    break;
            }
            unset($this->data['uniqueKeys']);
        }
    }

    public function setRequestHeaders() {
        $this->requestHeaders = (object) getallheaders();
        $this->requestHeaders->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
    }

    private function setResponseHeaders() {
        \Delight\Http\ResponseHeader::set('Access-Control-Allow-Origin', '*');
        \Delight\Http\ResponseHeader::set('Content-Type', 'application/json; charset=UTF-8');
        \Delight\Http\ResponseHeader::set('Access-Control-Allow-Methods', 'OPTIONS,GET,POST,PUT,DELETE');
        \Delight\Http\ResponseHeader::set('Access-Control-Max-Age', '3600');
        \Delight\Http\ResponseHeader::set('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    }

    protected function verifyClient() {
        if($this->verified) return true;
        if(isset($this->data['secretkey'])) {
            $config = new Config;
            $res = $this->verifyNonce($this->data['secretkey'], $config->Auth['publickey'] ?? 'apikey');
            if($res) return true;
        }
        if(isset($_SERVER[ 'HTTP_AUTHORIZATION' ]) || isset($_SERVER[ 'REDIRECT_HTTP_AUTHORIZATION' ])) {
            $auth = $_SERVER[ 'HTTP_AUTHORIZATION' ] ?? $_SERVER[ 'REDIRECT_HTTP_AUTHORIZATION' ];
            $nonce = str_replace('Bearer ', '', $auth);
            $res = $this->verifyNonce($nonce);
            if($res) {
                $this->verified = true;
            }
            return $res;
        }
        return false;
    }

    private function processFiles($folder = "all") {
        $folder = ucwords($folder);
        if(!isset($this->data['file_attachments'])) return;
        if(gettype($this->data['file_attachments']) == "string") {
            $field = $this->data['file_attachments'];
            if(!isset($this->data[$field])) return $this->data;
            $this->data['file_attachments'] = [];
            $this->data['file_attachments'][$field] = $this->data[$field];
        }
        $id = $this->data['id'] ?? 'new';
        foreach ($this->data['file_attachments'] as $key => $file) {
            $default = [
                "processor" => "uploadBase64Image",
                "path" => "Uploads/$folder/$id",
                "saveAs" => ""
            ];
            if(gettype($file) == 'array' || gettype($file) == 'object') {
                $file = array_merge($default, (array) $file);
            } else {
                if(isset($this->data[$file])) {
                    $key = $file;
                    $file = $this->data[$file];
                }
                $file = array_merge($default, ["data" => $file]);
            }
            $res = (new Files)->processFile($file);
            if($res) {
                $this->data[$key] = $res;
            } else $this->data[$key] = $this->data[$key] ?? "";
            unset($this->data['file_attachments']);
        }
    }

    protected function getResponse() {
        if($this->verifyClient()) {
            if($this->accessType == "public" || $this->getAuthorization()) {
                if(method_exists($this, $this->requestHeaders->requestMethod)) {
                    $method = $this->requestHeaders->requestMethod;
                    $this->$method();
                } else {
                    http_response_code(405);
                    $this->response = NULL;
                }
            } else {
                http_response_code(403);
                $this->response = NULL;
            }
        } else {
            http_response_code(401);
            $this->response = NULL;
        }
    	echo json_encode($this->response);
    }

    public function __destruct()
    {
        $this->getResponse();
    }
}