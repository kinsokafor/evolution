<?php 

namespace EvoPhp\Resources;

use EvoPhp\Database\Query;
use EvoPhp\Database\Session;
use EvoPhp\Api\Operations;
use EvoPhp\Database\DataType;
use EvoPhp\Api\Auth;
use EvoPhp\Api\FileHandling\Files;
use EvoPhp\Actions\Action;

/**
 * summary
 */
class User
{
    use Auth;

    use Meta;

    use JoinRequest;

    use DataType;
    /**
     * summary
     */

    private $args = [];

    private $resourceType = NULL;

    public $error = "";

    public $errorCode = 200;

    public $query;

    public $session;

    public $num_args;

    public $resultIds;

    public $result;

    private $init;

    private $tableCols = ["id", "username", "email", "password", "date_created", "meta"];

    public function __construct()
    {
        $this->query = new Query;
        $this->session = Session::getInstance();
    }

    public function __destruct() {
        // $this->execute();
    }

    public static function createTable() {
        $self = new self;

        $statement = "CREATE TABLE IF NOT EXISTS users (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL,
            email VARCHAR(50) NOT NULL,
            password TEXT NOT NULL,
            date_created TEXT NOT NULL,
            meta JSON NOT NULL
        )";
        $self->query->query($statement)->execute();

        $statement = "CREATE TABLE IF NOT EXISTS token (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                name TEXT NOT NULL,
                token TEXT NOT NULL,
                expiry BIGINT NOT NULL,
                scope TEXT NOT NULL,
                status VARCHAR(20) DEFAULT 'alive'
                )";
        $self->query->query($statement)->execute();
    }

    public static function maintainTable() {
        $self = new self;
        $statement = "ALTER TABLE user ADD 
                        (
                            meta JSON NOT NULL
                        )";
        $self->query->query($statement)->execute();

        $statement = "CREATE TABLE IF NOT EXISTS token (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT NOT NULL,
            name TEXT NOT NULL,
            token TEXT NOT NULL,
            expiry BIGINT NOT NULL,
            scope TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'alive'
            )";
        $self->query->query($statement)->execute();
    }

    public function execute() {
        if ($this->resourceType === NULL) return;

        $callback = $this->resourceType;
        if(method_exists($this, $callback)) {
            return $this->$callback();
        }
    }

    private function resetQuery() {
        $this->args = [];
        $this->num_args = 0;
    }

    public function new($meta = array(), ...$unique_keys) {

        if(is_object($meta)) {
            $meta = (array) $meta;
        }

        // preactivate account
        if(isset($meta['preactivate_account'])) {
			$preactivate = (bool) $meta['preactivate_account'];
			unset($meta['preactivate_account']);
		} else {
			$preactivate = Options::get('preactivate_all_registration');
		}

        // use unverified role
		if(isset($meta['unverified_role'])) {
			$unverified = (bool) $meta['unverified_role'];
			unset($meta['unverified_role']);
		} else {
			$unverified = Options::get('use_unverified_role');
		}

        $generatedUserName = $this->generateUsername($meta);
        $default = [
            "role" => ($unverified) ? "unverified" : Options::get("default_user_role"),
            "temp_role" => $meta['role'] ?? Options::get("default_user_role"),
            "status" => ($preactivate || $unverified) ? 'active' : 'inactive',
            "username" => $generatedUserName,
            "password" => $generatedUserName,
            "surname" => "",
            "other_names" => "",
            "email" => ""
        ];

        $meta = array_merge($default, $meta);
        $meta['phone'] = Operations::internationalizePhoneNumber($meta['phone'] ?? '', $meta['country_code'] ?? '');
        $meta['password'] = self::encrypt($meta['password']);

        if(Operations::count($unique_keys)) {
            $userObj = $this->getCount();
            foreach ($unique_keys as $meta_name) {
                if(!isset($meta[$meta_name])) continue;
                $userObj->where($meta_name, $meta[$meta_name]);
            }
            $res = $userObj->execute();
            if($res > 0) {
                $this->error = "User instance already exists";
                $this->errorCode = 101;
                return false;
            }
        }

        if ($meta['email'] !== "") {
            if($this->get($meta['email'])) {
                $this->error = "Sorry, email \"".$meta['email']."\" is already in use by another user.";
                return false;
            }
		}
		if ($this->get($meta['username'])) {
			$this->error = 'Sorry, username "'.$meta['username'].'" is already existing or created in error.';
			return false;
		}

        $otherMeta = array_diff_key($meta, array_flip($this->tableCols));

        $user_id = $this->query->insert('users', 'sssi', 
            [
                'username' => (string) $meta['username'], 
                'email' => $meta['email'], 
                'password' => $meta['password'], 
                'date_created' => time(),
                'meta' => json_encode($otherMeta)
            ]
        )->execute();

        unset($meta['username']);
        unset($meta['password']);

        if($user_id) {
            $action = new Action;
            $action->doAction("evoAfterSignUp", $user_id);
            return $user_id;
        }
        $this->error = ""; //set error message
        return false;

    }

    public function update($selector, $meta, ...$unique_keys) {
        
        if(is_object($meta)) {
            $meta = (array) $meta;
        }
        if(!$existing = $this->get($selector)) {
            return false;
        }
        if(Operations::count($unique_keys)) {
            $instance = $this->getUser();
            foreach($unique_keys as $unique_key) {
                if(!isset($meta[$unique_key])) continue;
                $instance->where($unique_key, $meta[$unique_key]);
            }
            $rows = $instance->execute();
            if(Operations::count($rows)) {
                foreach($rows as $row) {
                    if($row->id !== $existing->id) {
                        $this->error = "User instance already exists";
                        $this->errorCode = 101;
                        return false;
                    }
                }
            }
        }
        unset($meta['uniqueKeys']);
        unset($meta['id']);
        if(isset($meta['email']) && ($test = $this->get($meta['email']))) {
            if($test->id !== $existing->id) {
                $this->error = "Sorry, email \"".$meta['email']."\" is already in use by another user.";
                return false;
            }
        }

        if(isset($meta['password'])) {
            $meta['password'] = $this->encrypt($meta['password']);
        }

        $this->query->update("users")
                ->metaSet($meta, $this->tableCols, $existing->id)
                ->where("id", $existing->id)
                ->execute();

        return true;
    }

    public function generateUsername(array $meta = []) {
    
        if(isset($meta['username'])) return $meta['username'];
    
        if(isset($meta['user_name'])) return $meta['user_name'];
    
        if($option = Options::get('registration_use_field_for_username')) {
            if(isset($meta[$option]) && $meta[$option] != '' && $meta[$option] != null) {
                return Operations::applyFilters("username_filter", $meta[$option]);
            }
        }
        $prefix = ($option = Options::get('username_prefix')) ? trim($option) : "";
        do {
            $user_name = ($prefix !== "") ? $prefix.rand(10000, 99999).rand(10000, 99999) : rand(10000, 99999).rand(10000, 99999).'-'.rand(100, 999);
        } while ($this->get($user_name));
        return Operations::applyFilters("username_filter", $user_name);
    }

    public function get($selector) {

        if(gettype($selector) === "integer") {
            $selectColumn = "id";
            $selector = $selector;
            $selectorType = "i";
        } else {
            if(filter_var($selector, FILTER_VALIDATE_EMAIL)) {
                $selectColumn = "email";
                $selector = (string) $selector;
                $selectorType = "s";
            } else {
                $selectColumn = "username";
                $selector = (string) $selector;
                $selectorType = "s";
            }
        }

        $res = $this->query->select("users")
                ->where($selectColumn, $selector, $selectorType)
                ->execute()->row();
        
        $meta = self::merge($res);
        
        return $this->processJoinRequest($meta);
    }

    public function delete($userId) {
        $this->query->delete("users")->where("id", $userId, "i")->execute();
        $files = new Files;
        $files->deleteDir("Uploads/$userId");
    }

    public function deleteUser() {
        $this->resetQuery();
        $this->getUser();
        $this->resourceType = "deleteUserByMetaData";
        return $this;
    }

    public function getUser() {
        $this->resetQuery();
        $this->resourceType = "getUserByMetaData";
        $this->query->select("users");
        return $this;
    }

    public function getCount() {
        $this->resetQuery();
        $this->query->select("users", "COUNT(id) as count");
        $this->resourceType = "getUserCountByMetaData";
        return $this;
    }

    public function getIds() {
        $this->resetQuery();
        $this->query->select("users", "id");
        $this->resourceType = "getUserIdsByMetaData";
        return $this;
    }

    public function isMeta($column) {
        return !in_array($column, $this->tableCols);
    }

    public function where($column, $value, $type = "s", $rel = "LIKE") {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        if(is_array($value)) {
            $this->query->whereIn($column, $this->evaluateData($value[0] ?? "")->valueType, $rel);
        } else {
            $this->query->where($column, $value, $type = false, $rel);
        }
        
        return $this;
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
        $this->query->whereIn($column, $type, ...$values);
        return $this;
    }

    public function whereNotIn($column, $type, ...$values) {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        $this->query->whereNotIn($column, $type, ...$values);
        return $this;
    }
    
    public function whereBetween($column, $v1, $v2, $type = false) {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        $this->query->whereBetween($column, $v1, $v2, $type);
        return $this;
    }

    public function orderBy($column, $order = 'ASC') {
        if($this->isMeta($column)) {
            $column = "JSON_UNQUOTE(JSON_EXTRACT(meta, '$.$column'))";
        }
        $this->query->orderBy($column, $order);
        return $this;
    }

    public function limit($limit) {
        $this->query->limit($limit);
        return $this;
    }

    public function offset($offset) {
        $this->query->offset($offset);
        return $this;
    }

    private function getUserByMetaData() {
        $this->result = $this->query->execute()->rows();
        $this->result = array_map(function($v){
            return $this::merge($v);
        }, $this->result);
        return $this->result;
    }

    private function getUserCountByMetaData() {
        $this->result = $this->query->execute()->row();
        return $this->result->count;
    }

    private function getUserIdsByMetaData() {
        $this->resultIds = $this->query->execute()->rows();
        $this->result = array_map(function($v){
            return $v->id;
        }, $this->resultIds);
        return $this->result;
    }

    public function deleteUserByMetaData() {
        $ids = $this->getUserIdsByMetaData();
        if(Operations::count($ids)) {
            foreach ($ids as $userId) {
                $this->delete($userId);
            }
        }
    }

    public function changePassword($data) {
        $session = Session::getInstance();
        $tokenObj = $this->getTokenObject($session->accesstoken);
        if(!$tokenObj) {
            return [
                "status" => false,
                "message" => "Invalid session. Please sign in again."
            ];
        }
        $user = $this->get($tokenObj->user_id);
        if(!$user) {
            return [
                "status" => false,
                "message" => "Something went wrong. Please sign in again."
            ];
        }
        if($this->encrypt($data['oldPassword']) !== $user->password) {
            return [
                "status" => false,
                "message" => "Current password is incorrect."
            ];
        }
        $this->update($tokenObj->user_id, ['password' => $data['password']]);
        return [
            "status" => true,
            "message" => "Done"
        ];
    }

    public function changeUserPassword($data) {
        $session = Session::getInstance();
        $tokenObj = $this->getTokenObject($session->accesstoken);
        if(!$tokenObj) {
            return [
                "status" => false,
                "message" => "Invalid session. Please sign in again."
            ];
        }
        $user = $this->get($tokenObj->user_id);
        if(!$user) {
            return [
                "status" => false,
                "message" => "Something went wrong. Please sign in again."
            ];
        }
        if($this->encrypt($data['yourPassword']) !== $user->password) {
            return [
                "status" => false,
                "message" => "Your password is incorrect."
            ];
        }
        $this->update($data['user_id'], ['password' => $data['password']]);
        return [
            "status" => true,
            "message" => "Done"
        ];
    }

}