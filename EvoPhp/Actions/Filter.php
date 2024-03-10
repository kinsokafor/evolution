<?php

namespace EvoPhp\Actions;

use EvoPhp\Api\Operations;

class Filter extends Action
{

    public function __construct() {
        parent::__construct();
    }

    public static function add($filter, $cb, $priority = 1, $auth = '') {
        $self = new self;
        return $self->addAction($filter, $cb, $priority, $auth);
    }

    public function addFilter($filter, $cb, $priority = 1, $auth = '') {
        return parent::addAction($filter, $cb, $priority, $auth);
    }

    public static function apply($filter, $var, $args = array()) {
        $self = new self;
        return $self->applyFilters($filter, $var, $args);
    }

    public function applyFilters($filter, $var, $args = array()) {
        $res = $this->query->select("actions")->where("action_name", $filter)->orderBy("priority")->execute()->rows();
        if(Operations::count($res)) {
            foreach ($res as $key => $value) {
                $test = explode("::", $value->action_cb);
                if(Operations::count($test) > 1) {
                    list($class, $method) = $test;
                    if(method_exists($class, $method)) {
                        if(!empty($args)) {
                            $var = call_user_func(array($class, $method), $var, $args);
                        } else {
                            $var = call_user_func(array($class, $method), $var);
                        }
                    }
                } else {
                    if(function_exists($value->action_cb)) {
                        if(!empty($args)) {
                            $var = call_user_func($value->action_cb, $var, $args);
                        } else {
                            $var = call_user_func($value->action_cb, $args);
                        }
                    }
                }
			}
        }
        return $var;
    }
}

?>