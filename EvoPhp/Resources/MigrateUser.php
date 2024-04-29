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

    public static function migrate() {
        self::maintainTable();
        $dbTable = new DbTable;
        $v = $dbTable->select("users", "id")->where("meta", "null")->limit(20)->execute()->rows();
        if(Operations::count($v)) {
            $user = new User;
            foreach($v as $vv) {
                $meta = $user->get($vv->id);
                if($meta == false) $meta = [];
                $dbTable->update("users")->metaSet($meta, [
                    "id", "username", "email", "password", "meta", "date_created"
                ])->where("id", $vv->id)->execute();
            }
        } else {
            echo "Done!!!";
        }
    }
}

?>