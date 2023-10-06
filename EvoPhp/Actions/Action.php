<?php

namespace EvoPhp\Actions;

use EvoPhp\Api\Operations;
use EvoPhp\Database\Query;

class Action
{
    public $query;

    public function __construct() {
        $this->query = new Query;
    }

    public static function createTable() {
        $self = new self;
        if($self->query->checkTableExist("actions")) {
            $self->maintainTable();
            return;
        }

        $statement = "CREATE TABLE IF NOT EXISTS actions (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            action_name VARCHAR(100) NOT NULL,
            action_cb TEXT NOT NULL,
            priority INT UNSIGNED,
            auth VARCHAR(100) NOT NULL DEFAULT ''
            )";
        $self->query->query($statement)->execute();
    }

    private function maintainTable() {
        $statement = "ALTER TABLE actions ADD 
                        (
                            auth VARCHAR(100) NOT NULL DEFAULT ''
                        )";
        $this->query->query($statement)->execute();
    }

    public static function do($action, $args = array()) {
        $instance = new self;
        return $instance->doAction($action, $args);
    }

    public function doAction($action, $args = array()) {
        ob_start();
        $res = $this->query->select("actions")->where("action_name", $action)->orderBy("priority")->execute()->rows();
        if(Operations::count($res)) {
            foreach ($res as $key => $value) {
                $test = explode("::", $value->action_cb);
                if(Operations::count($test) > 1) {
                    list($class, $method) = $test;
                    if(method_exists($class, $method)) {
                        if(!empty($args)) {
                            call_user_func(array($class, $method), $args);
                        } else {
                            call_user_func(array($class, $method));
                        }
                    }
                } else {
                    if(function_exists($value->action_cb)) {
                        if(!empty($args)) {
                            call_user_func($value->action_cb, $args);
                        } else {
                            call_user_func($value->action_cb);
                        }
                    }
                }
			}
        }
        $template = ob_get_contents();
        ob_get_clean();
        return $template;
    }

    public static function add($action, $cb, $priority = 1, $auth = []) {
        $instance = new self;
        return $instance->addAction($action, $cb, $priority, $auth);
    }

    public function addAction($action, $cb, $priority = 1, $auth = '') {
        $action = \strtolower($action);
        $res = $this->query->select("actions")
                    ->where("action_name", $action)
                    ->execute()->rows();
        if(Operations::count($res)){
            foreach ($res as $key => $value) {
                if($value->action_cb == $cb) return false;
            }
        }
        return $this->query->insert("actions", "ssis", [
            "action_name" => $action,
            "action_cb" => $cb,
            "priority" => $priority,
            "auth" => $auth
        ]);
    }
}
