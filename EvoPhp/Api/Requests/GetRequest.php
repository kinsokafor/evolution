<?php 

namespace EvoPhp\Api\Requests;

use EvoPhp\Api\Operations;
use EvoPhp\Resources\Post;
use EvoPhp\Resources\User;
use EvoPhp\Resources\Store;
use EvoPhp\Resources\Options;
use EvoPhp\Resources\Records;
use EvoPhp\Resources\DbTable;

class GetRequest implements RequestInterface {

    public static function postTable($request)
    {
        $post = new Post;
        if($request->joinUserAt != null) $post->joinUserAt($request->joinUserAt);
        if($request->joinPostAt != null) $post->joinPostAt($request->joinPostAt);
        if($request->joinAt != null && $request->joinTable != null) $post->joinAt($request->joinTable, $request->joinAt);
        if(isset($request->data['id'])) {
            $request->response = $post->get($request->data['id']);
            if($request->response) {
                http_response_code(200);
            } else http_response_code(404);
        } 
        else if(isset($request->data['type'])) {
            $type = $request->data['type'];
            unset($request->data['type']);
            if($request->isCount) {
                $post->getCount($type);
            } else $post->getPost($type);
            $post->whereGroup($request->data);
            if($request->limit) $post->limit($request->limit);
            if($request->offset) $post->offset($request->offset);
            if($request->order_by) $post->orderBy($request->order_by, $request->order ? $request->order : 'ASC');
            http_response_code(200);
            $request->response = $post->execute();
        }
        else {
            http_response_code(422);
            $request->response = null;
        }
    }

    public static function storeTable($request)
    {
        $store = new Store;
        if($request->joinUserAt != null) $store->joinUserAt($request->joinUserAt);
        if($request->joinPostAt != null) $store->joinPostAt($request->joinPostAt);
        if($request->joinAt != null && $request->joinTable != null) $store->joinAt($request->joinTable, $request->joinAt);
        if(isset($request->data['id'])) {
            $request->response = $store->get($request->data['id'])->execute();
            if($request->response) {
                http_response_code(200);
            } else http_response_code(404);
        } 
        else if(isset($request->data['type'])) {
            $type = $request->data['type'];
            unset($request->data['type']);
            if($request->isCount) {
                $store->getCount($type);
            } else $store->select($type);
            $store->whereGroup($request->data);
            if($request->limit) $store->limit($request->limit);
            if($request->offset) $store->offset($request->offset);
            if($request->order_by) $store->orderBy($request->order_by, $request->order ? $request->order : 'ASC');
            http_response_code(200);
            $request->response = $store->execute()->rows();
        }
        else {
            http_response_code(422);
            $request->response = null;
        }
    }

    public static function usersTable($request) {
        $user = new User;
        if($request->joinUserAt != null) $user->joinUserAt($request->joinUserAt);
        if($request->joinPostAt != null) $user->joinPostAt($request->joinPostAt);
        if($request->joinAt != null && $request->joinTable != null) $user->joinAt($request->joinTable, $request->joinAt);
        if(isset($request->data['id'])) {
            $request->response = $user->get((int) $request->data['id']);
            if($request->response) {
                http_response_code(200);
            } else http_response_code(404);
        } 
        else if(isset($request->data['email'])) {
            $request->response = $user->get($request->data['email']);
            if($request->response) {
                http_response_code(200);
            } else http_response_code(404);
        } 
        else if(isset($request->data['username'])) {
            $request->response = $user->get($request->data['username']);
            if($request->response) {
                http_response_code(200);
            } else http_response_code(404);
        }
        else if(isset($request->data['selector'])) {
            $request->response = $user->get($request->data['selector']);
            if($request->response) {
                http_response_code(200);
            } else http_response_code(404);
        } 
        else {
            if($request->isCount) {
                $user->getCount();
            } else $user->getUser();
            $user->whereGroup($request->data);
            if($request->limit) $user->limit($request->limit);
            if($request->offset) $user->offset($request->offset);
            if($request->order_by) $user->orderBy($request->order_by, $request->order ? $request->order : 'ASC');
            http_response_code(200);
            $request->response = $user->execute();
        }
    }

    public static function optionsTable($request) {
        $option = new Options;
        if(isset($request->data['key'])) {
            if(strpos($request->data['key'], ',')) {
                $request->data['key'] = \EvoPhp\Api\Operations::trimArray(\explode(',', $request->data['key']));
            }
            $request->response = $option->getOption($request->data['key'], $request->cache, $request->cacheExpiry);
            if($request->response !== NULL) {
                http_response_code(200);
            } else http_response_code(404);
        } else {
            http_response_code(422);
            $request->response = null;
        }
    }

    public static function recordsTable($request) {
        $record = new Records;
        if(isset($request->data['key'])) {
            if(strpos($request->data['key'], ',')) {
                $request->data['key'] = \EvoPhp\Api\Operations::trimArray(\explode(',', $request->data['key']));
            }
            $request->response = $record->getRecord($request->data['key'], $request->cache, $request->cacheExpiry);
            if($request->response !== NULL) {
                http_response_code(200);
            } else http_response_code(404);
        } else {
            http_response_code(422);
            $request->response = null;
        }
    }

    public static function dbTable($request) {
        $query = new DbTable;
        $selection = $request->isCount ? "COUNT(*) AS count" : "*";
        $query->select($request->tableName, $selection)->whereGroup($request->data);
        if($request->joinUserAt != null) $query->joinUserAt($request->joinUserAt, ...$request->rightColumns);
        if($request->joinPostAt != null) $query->joinPostAt($request->joinPostAt, ...$request->rightColumns);
        if($request->joinAt != null && $request->joinTable != null) $query->joinAt($request->joinTable, $request->joinAt, ...$request->rightColumns);
        if(isset($request->data['id'])) {
            $request->response = $query->execute()->row();
            if($request->response !== null) {
                http_response_code(200);
            } else http_response_code(404);
        } else {
            if($request->limit) $query->limit($request->limit);
            if($request->offset) $query->offset($request->offset);
            if($request->order_by) $query->orderBy($request->order_by, $request->order ? $request->order : 'ASC');
            http_response_code(200);
            $request->response = $query->execute()->rows();
        }
    }
}