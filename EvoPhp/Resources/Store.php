<?php 

namespace EvoPhp\Resources;

use EvoPhp\Database\Session;
use EvoPhp\Api\Operations;
use EvoPhp\Database\DataType;

/**
 * summary
 */
class Store extends DbTable
{
    use JoinRequest;
    use DataType;
    /**
     * summary
     */

    public $session;

    private $callback;

    public $error;

    public $errorCode;

    private $tableCols = ['id', 'type', 'last_altered_by', 'time_altered'];

    public function __construct()
    {
        $this->session = Session::getInstance();
    }

    public function __destruct() {
        // $this->execute();
    }

    public function execute() {
        $res = parent::execute();
        switch ($this->callback) {
            case 'isCount':
                return $res->row()->count;
                break;

            case 'single':
                return $res->row();
                break;
            
            default:
                return $res;
                break;
        }
    }

    public static function createTable() {
        $self = new self;

        $statement = "CREATE TABLE IF NOT EXISTS store (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `type` VARCHAR(100) NOT NULL,
                `meta` JSON NOT NULL,
                time_altered TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_altered_by BIGINT NOT NULL
                )";
        $self->query($statement)->execute();
    }

    private function cleanMeta($meta) {
        unset($meta['id']);
        unset($meta['type']);
        unset($meta['last_altered_by']);
        unset($meta['time_altered']);
        return Operations::sanitize($meta);
    }

    private function isMeta($column) {
        return !in_array($column, $this->tableCols);
    }

    public function new($type, $meta = array(), ...$unique_keys) {

        if(is_object($meta)) {
            $meta = (array) $meta;
        }
        
        $meta = $this->cleanMeta($meta);

        if(Operations::count($unique_keys)) {
            $instance = new self;
            $instance->getCount($type);
            foreach ($unique_keys as $meta_name) {
                if(!isset($meta[$meta_name])) continue;
                $instance->where($meta_name, $meta[$meta_name]);
            }
            $res = $instance->execute();
            if($res > 0) {
                $this->error = "Store instance already exists";
                $this->errorCode = 101;
                return false;
            }
        }
        
        return parent::insert("store", "sis", [
            "type" => $type,
            "last_altered_by" => $this->session->getResourceOwner()->user_id,
            "meta" => json_encode($meta)
        ])->execute();
    }

    public function select($type, $column = "*") {
        return parent::select('store', $column)->where('type', $type);
    }

    public function getCount($type) {
        $this->callback = "isCount";
        return $this->select($type, "COUNT(id) as count");
    }

    public function get($id, $column = "*") {
        $this->callback = "single";
        return parent::select('store', $column)->where('id', $id);
    }

    public function selectDistinct($type, $column = "*") {
        return parent::selectDistinct('store', $column)->where('type', $type);
    }

    public function delete($type) {
        if(gettype($type) == 'integer') {
            return parent::delete('store')->where('id', $type);
        }
        return parent::delete('store')->where('type', $type);
    }

    public function insert($table, $dataTypes, ...$data) {
        throw new \Exception('Insert method not allowed. Use `new` menthod instead');
    }

    public function where($column, $value, $type = false, $rel = "LIKE") {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        return parent::where($column, $value, $type = false, $rel);
    }

    public function whereGroup(array $meta = [], string | array $rel = "LIKE") {
        if(!Operations::count($meta)) return $this;
        foreach ($meta as $meta_name => $meta_value) {
            $rel = (is_array($rel) && isset($rel[$meta_name])) ? $rel[$meta_name] : $rel;
            $this->where(
                $meta_name, 
                $meta_value, 
                $this->evaluateData($meta_value)->valueType, 
                $rel
            );
        }
        return $this;
    }

    public function whereIn($column, $type, ...$values) {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        return parent::whereIn($column, $type, ...$values);
    }

    public function whereNotIn($column, $type, ...$values) {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        return parent::whereNotIn($column, $type, ...$values);
    }
    
    public function whereBetween($column, $v1, $v2, $type = false) {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        return parent::whereBetween($column, $v1, $v2, $type);
    }

    public function orderBy($column, $order = 'ASC') {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        return parent::orderBy($column, $order);
    }

    public function update($table = "store") {
        return parent::update("store")->set('time_altered', date('Y-m-d h:i:s'))
        ->set('last_altered_by', $this->session->getResourceOwner() ? $this->session->getResourceOwner()->user_id : 0);
    }

    public function metaSet(
        array|object $data, 
        array $tableCols = [], 
        array|object|int $oldMeta = [], 
        NULL|string $table = "store") {
        return parent::metaSet($data, $this->tableCols, $oldMeta, "store");
    }

    public function metaDel(array $data, 
        array|object|int $oldMeta = [], 
        NULL|string $table = "store") {
        return parent::metaDel($data, $oldMeta, "store");
    }
}