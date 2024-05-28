<?php

namespace EvoPhp\Resources;

use EvoPhp\Database\Query;
use EvoPhp\Resources\User;
use EvoPhp\Resources\DbTable;
use EvoPhp\Api\Operations;

final class MigrateUser
{
    public $query;

    public function __construct() {
        $this->query = new Query;
    }

    public static function maintainTable() {
        $self = new self;
        $statement = "ALTER TABLE users ADD
                        (
                            meta JSON NOT NULL
                        )";
        $self->query->query($statement)->execute();
    }

    private function get($selector) {
        $user = new User;
        $selectColumn = "user_id";
        $selector = $selector;
        $selectorType = "i";
        
        
        $stmt = "SELECT DISTINCT meta_name, meta_value, data_type, meta_int, meta_double, meta_blob, username, email, users.id, password, date_created
            FROM user_meta 
            LEFT JOIN users ON users.id = user_meta.user_id
            WHERE user_id IN (SELECT id FROM users WHERE $selectColumn = ?)
            ORDER BY meta_value ASC";

        $res = $this->query->query($stmt, $selectorType, $selector)->execute()->rows("OBJECT_K");
        if(empty($res)) {
            $user->error = "User not found";
            $user->errorCode = 500;
            return false;
        }
        $meta = array_map(function($v){
            switch ($v->data_type) {
                case "int":
                case "intege":
                case "integer":
                case "number":
                    return $v->meta_int;
                    break;

                case "boolean":
                case "boolea":
                    return ($v->meta_int === 0) ? false : true;
                    break;

                case "double":
                case "float":
                    return $v->meta_double;
                    break;

                case "blob":
                    return $v->meta_blob;
                    break;

                case "array":
                    return Operations::unserialize($v->meta_value);
                    break;

                case "object":
                    return (object) Operations::unserialize($v->meta_value);
                    break;

                default:
                    $meta_value = htmlspecialchars_decode($v->meta_value);
                    return Operations::removeslashes($meta_value); 
                    break;
            }
            
        }, $res);
        $meta['email'] = $res['role']->email;
        $meta['username'] = $res['role']->username;
        $meta['password'] = $res['role']->password;
        $meta['date_created'] = $res['role']->date_created;
        $meta['id'] = $res['role']->id;
        $meta = (object) $meta;
        return $meta;
    }

    public static function migrate() {
        echo "Initiating!!!<br/>";
        $self = new self;
        self::maintainTable();
        $dbTable = new DbTable;
        $v = $dbTable->select("users", "id")->where("meta", "null")->limit(20)->execute()->rows();
        if(Operations::count($v)) {
            foreach($v as $vv) {
                $meta = $self->get($vv->id);
                if($meta == false) $meta = [];
                $dbTable->update("users")->metaSet($meta, [
                    "id", "username", "email", "password", "meta", "date_created"
                ])->where("id", $vv->id)->execute();
            }
            echo "Done 20. Please refresh.";
        } else {
            echo "Done!!!";
        }
    }
}

?>