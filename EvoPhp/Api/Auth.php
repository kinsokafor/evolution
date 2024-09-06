<?php

namespace EvoPhp\Api;

use EvoPhp\Api\Config;
use EvoPhp\Database\Query;
use EvoPhp\Resources\User;
use EvoPhp\Resources\DbTable;
use EvoPhp\Database\Session;
use EvoPhp\Api\Operations;
use Delight\Cookie\Cookie;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use EvoPhp\Actions\Action;

Trait Auth {

    public $accessToken;

    public $resourceOwner;

    public $authorizationState = false;

    protected string $accessType = "protected";

    protected array $accessLevel;

    static public function encrypt($verb) {
        $config = new Config;
        if($config->hashing != NULL) {
            $test = explode("::", $config->hashing);
            try {
                if(Operations::count($test) > 1) {
                    list($class, $method) = $test;
                    if(method_exists($class, $method)) {
                        return call_user_func(array($class, $method), $verb);
                    } else {
                        return SHA1(md5($verb.$config->salt.$verb));
                    }
                } else {
                    if(function_exists($config->hashing)) {
                        return call_user_func($config->hashing, $verb);
                    } else {
                        return SHA1(md5($verb.$config->salt.$verb));
                    }
                }
            } catch (\Exception $feedback) {
                return SHA1(md5($verb.$config->salt.$verb));
            }
        }
        return SHA1(md5($verb.$config->salt.$verb));
    }

    public static function isSignedIn() {
        $obj = new self;
        $session = Session::getInstance();
        if(!isset($session->accesstoken)) return ["loginStatus" => false];
        if($tokenMeta = $obj->getTokenObject($session->accesstoken)) {
            $user = new User;
            return [
                "loginStatus" => true, 
                "currentUser" => $user->get($tokenMeta->user_id),
                "userScope" => Operations::unserialize($tokenMeta->scope),
                "token" => $session->accesstoken,
                "expiry" => $tokenMeta->expiry
            ];
        }
        return ["loginStatus" => false];
    }

    public static function signIn($selector, $password) {
        $user = new User;
        $meta = $user->get($selector);
        $session = Session::getInstance();
        if(!$meta) :
            $session->increment("failedSignInAttempts");
            return ['loginStatus' => false, 'msg' => 'Incorrect Username'];
        endif;
        $password = self::encrypt($password);
        if($meta->password !== $password) :
            $session->increment("failedSignInAttempts");
            return ['loginStatus' => false, 'msg' => 'Incorrect Password'];
        endif;
        if($meta->status !== "active") :
            return ['loginStatus' => false, 'msg' => 'Sign in disallowed for '.$meta->status.' account'];
        endif;
        $session->failedSignInAttempts = 0;
        $instance = new self;
        $instance->resourceOwner = $meta;
        $instance->authorizationState = true;
        $scope = $instance->getScope($meta->role);
        return $instance->createToken($meta, $scope);
    }

    public static function pushLogin($meta) {
        if(Operations::checkAccess([1,2])) {
            $instance = new self;
            $instance->resourceOwner = $meta;
            $instance->authorizationState = true;
            $scope = $instance->getScope($meta->role);
            return $instance->createToken($meta, $scope);
        } else {
            die("Wrong access");
        }
    }

    public function getScope($role) {
        $config = new Config;
        $roles = $config->Auth["roles"];
        return ($roles[$role] ?? $roles[$config->Auth["defaultRole"]])['capacity'];
    }

    public static function signOut() {
        $query = new Query;
        $session = Session::getInstance();
        $existing = $query->select("token")
            ->where("token", $session->accesstoken, "s")
            ->execute()
            ->rows();
        $query->delete("token")
            ->where("token", $session->accesstoken, "s")
            ->execute();
        $session->destroy();
        $cookie = new Cookie("nonce");
        $cookie->delete();
        return $existing;
    }

    private function createToken($meta, $scope = []) {
        $token_name = Operations::getFullname($meta).": SignIn";
        $query = new Query;
        $session = Session::getInstance();
        $token = Operations::randomString();
        $config = new Config;
        $expiry = \time() + $config->Auth["tokenLifetime"];
        $scope = Operations::serialize($scope);
        $query->delete("token")
        ->where("user_id", $this->resourceOwner->id, "i")
        ->where("expiry", time(), "i", "<")
        ->execute();
        $query->insert("token", "ississ", [
            "user_id" => $this->resourceOwner->id,
            "name" => $token_name,
            "token" => $token,
            "expiry" => $expiry,
            "scope" => $scope,
            "status" => "active"
        ])->execute();
        $nonce = $this->createNonce($token);
        $cookie = new Cookie("nonce");
        $cookie->setValue($nonce)->setMaxAge($config->Auth["tokenLifetime"])->save();
        $session->accesstoken = $token;
        $index = Operations::getIndex($meta);
        $action = new Action;
        $action->doAction("evoAfterLogin", $meta);
        return [
            'loginStatus' => true, 
            'token' => $token, 
            'msg' => 'Login Successful', 
            'index' => $index
        ];
    }

    protected function getTokenObject($token) {
        $dbTable = new DbTable;
        $res = $dbTable->select("token")
                    ->leftJoin("users", "token.user_id", "=", "users.id")
                    ->where("token", $token)
                    ->where("status", "active")
                    ->where("expiry", time(), "i", ">")
                    ->execute()->row();
        if($res == null) {
            return false;
        }
        return $dbTable->merge($res);
    }

    protected function getAuthorization() {
        if(!\Delight\Cookie\Cookie::exists('nonce')) :
            return false;
        endif;

        if(!$this->verifyNonce(\Delight\Cookie\Cookie::get('nonce'))) :
            return false;
        endif;
        
        $session = Session::getInstance();
        
        $tokenObj = $this->getTokenObject($session->accesstoken);

        $scope = Operations::unserialize($tokenObj->scope);

        if(count($this->accessLevel) < 1) return true;

        if(!Operations::count(array_intersect($scope, $this->accessLevel))) :
            return false;
        endif;

        return true;
    }

    protected function getKey() {
        $session = Session::getInstance();
        if(isset($session->accesstoken)) {
            if($tokenMeta = $this->getTokenObject($session->accesstoken)) {
                return $tokenMeta->token;
            }
        }
        return 'public_key';
    }

    protected function getNonce() {
        if(\Delight\Cookie\Cookie::exists('nonce')) {
            return \Delight\Cookie\Cookie::get('nonce');
        }
        return $this->createNonce();
    }

    protected function createNonce($key = false, int $nbf = 1357000000) {
        if(!$key)
            $key = $this->getKey();
        $config = new Config;
        $payload = [
            'iss' => $config->root,
            'aud' => $config->root,
            'iat' => time(),
            'nbf' => $nbf
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    protected function verifyNonce($jwt, $key = false) {
        if(!$key) {
            $key = $this->getKey();
        }
        $config = new Config;
        try {
            
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            // Validate issuer
            if ($decoded->iss !== $config->root) {
                return false;
            }

            // Validate audience
            // if ($decoded->aud !== 'my-api-client') {
            //     throw new \Exception('Invalid audience');
            // }

            return true;
        }
        catch(\Firebase\JWT\ExpiredException $e) {
            return false;
        }
        catch (\Firebase\JWT\SignatureInvalidException $e) {
            return false;
        }
    }

}

?>