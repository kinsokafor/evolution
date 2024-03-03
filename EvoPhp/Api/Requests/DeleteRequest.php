<?php 

namespace EvoPhp\Api\Requests;

use EvoPhp\Resources\Post;
use EvoPhp\Resources\User;
use EvoPhp\Resources\Store;
use EvoPhp\Database\Query;
use EvoPhp\Api\Operations;
use EvoPhp\Database\DataType;

class DeleteRequest implements RequestInterface {

    use DataType;

    public static function postTable($request) {
        $post = new Post;
        if(isset($request->data['id'])) {
            $post->delete((int) $request->data['id']);
            http_response_code(200);
            $request->response = null;
        }
        else {
            if(!isset($request->data['type'])) {
                http_response_code(422);
                $request->response = "Please provide post type";
            } else {
                $post->deletePost($request->data['type'])->whereGroup($request->data)->execute();
                if($post->error !== "") {
                    http_response_code(422);
                    $request->response = $post->error;
                } else {
                    http_response_code(200);
                    $request->response = null;
                }
            }
            
        }
    }

    public static function storeTable($request) {
        $store = new Store;
        if(isset($request->data['id'])) {
            $store->delete((int) $request->data['id'])->execute();
            http_response_code(200);
            $request->response = null;
        }
        else {
            if(!isset($request->data['type'])) {
                http_response_code(422);
                $request->response = "Please provide post type";
            } else {
                $store->delete($request->data['type'])->whereGroup($request->data)->execute();
                if($store->error !== "") {
                    http_response_code(422);
                    $request->response = $store->error;
                } else {
                    http_response_code(200);
                    $request->response = null;
                }
            }
            
        }
    }

    public static function usersTable($request) {
        $user = new User;
        if(isset($request->data['id'])) {
            $user->delete((int) $request->data['id']);
            http_response_code(200);
            $request->response = null;
        }
        else {
            $user->deleteUser()->whereGroup($request->data)->execute();
            if($user->error !== "") {
                http_response_code(422);
                $request->response = $user->error;
            } else {
                http_response_code(200);
                $request->response = null;
            }
        }
    }

    public static function optionsTable($request) {
        PostRequest::optionsTable($request);
    }

    public static function recordsTable($request) {
        PostRequest::recordsTable($request);
    }

    public static function dbTable($request) {
        if(Operations::count($request->data)) {
            $query = new Query;
            return $query->delete($request->tableName)->whereGroup($request->data)->execute();
        } else {
            http_response_code(400);
            $request->response = NULL;
        }
    }

}