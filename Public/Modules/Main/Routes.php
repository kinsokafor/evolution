<?php

use EvoPhp\Api\Operations;
use Public\Modules\Main\MainController;
use EvoPhp\Api\Requests\Requests;
use EvoPhp\Database\Session;
use EvoPhp\Themes\Templates;

//INSTALL
$router->post('/install/plugin', function(){
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $plugin = $params['plugin'];
    echo require_once("./Public/Modules/$plugin/Install.php");
});

// API ROUTES
$router->group('/api/user', function () use ($router) {
    $router->get('/', function () {
        $request = new Requests;
        $request->user()->auth(3,4);
    });
    $router->get('/id/{id}', function ($params) {
        $request = new Requests;
        $params["id"] = (int) $params["id"];
        $request->user($params)->auth(1,2,3,4,5,6,7,8,9);
    });
    $router->get('/username/{username}', function ($params) {
        $request = new Requests;
        $request->user($params)->auth(1,2,3,4,5,6,7,8,9);
    });
    $router->delete('/', function ($params) {
        $request = new Requests;
        $request->user($params)->auth(1,2,3);
    });
    $router->delete('/id/{id}', function ($params) {
        $request = new Requests;
        $params["id"] = (int) $params["id"];
        $request->user($params)->auth(1,2,3);
    });
    $router->put('/id/{id}', function ($params) {
        $request = new Requests;
        $params["id"] = (int) $params["id"];
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->user($params)->auth(5,6);
    });
    $router->post('/', function ($params) {
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->user($params)->auth(1);
    });
});

$router->group('/api/post', function () use ($router) {
    $router->get('/', function () {
        $request = new Requests;
        $request->post()->auth();
    });
    $router->get('/id/{id}', function ($params) {
        $request = new Requests;
        $request->post($params)->auth();
    });
    $router->get('/type/{type}', function ($params) {
        $request = new Requests;
        $request->post($params)->auth();
    });
    $router->put('/id/{id}', function ($params) {
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->post($params)->auth(5,6);
    });
    $router->delete('/', function ($params) {
        $request = new Requests;
        $request->post($params)->auth(1,2,3);
    });
    $router->delete('/id/{id}', function ($params) {
        $request = new Requests;
        $request->post($params)->auth(1,2,3);
    });
    $router->post('/{type}', function ($params) {
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->post($params)->auth(1,2,3,4,5,6,7,8,9);
    });
});

$router->group('/api/store', function () use ($router) {
    $router->get('/', function () {
        $request = new Requests;
        $request->store()->auth();
    });
    $router->get('/id/{id}', function ($params) {
        $request = new Requests;
        $request->store($params)->auth();
    });
    $router->get('/type/{type}', function ($params) {
        $request = new Requests;
        $request->store($params)->auth();
    });
    $router->put('/id/{id}', function ($params) {
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->store($params)->auth(5,6);
    });
    $router->delete('/', function ($params) {
        $request = new Requests;
        $request->store($params)->auth(1,2,3);
    });
    $router->delete('/id/{id}', function ($params) {
        $request = new Requests;
        $request->store($params)->auth(1,2,3);
    });
    $router->post('/{type}', function ($params) {
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->store($params)->auth(1,2,3,4,5,6,7,8,9);
    });
});

$router->group('/api/dbtable/{table}', function () use ($router) {
    $router->get('/', function($params){
        $request = new Requests;
        $table = $params['table'];
        unset($params['table']);
        $request->{$table}($params)->auth(1,2,3,4,5,6,7,8,9);
    });
    $router->get('/id/{id}', function($params){
        $request = new Requests;
        $table = $params['table'];
        unset($params['table']);
        $request->{$table}($params)->auth(1,2,3,4,5,6,7,8,9);
    });
    $router->put('/id/{id}', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $table = $params['table'];
        unset($params['table']);
        $request->{$table}($params)->auth(5,6);
    });
    $router->post('/{table}', function ($params) {
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $table = $params['table'];
        unset($params['table']);
        $request->{$table}($params)->auth(1,2,3);
    });
    $router->delete('/id/{id}', function ($params) {
        $request = new Requests;
        $table = $params['table'];
        unset($params['table']);
        $request->{$table}($params)->auth(1,2,3);
    });
    $router->delete('/', function ($params) {
        $request = new Requests;
        $table = $params['table'];
        unset($params['table']);
        $request->{$table}($params)->auth(1,2,3);
    });
});


$router->group('/api/options/{key}', function () use ($router) {
    $router->get('/', function($params){
        $request = new Requests;
        $request->options($params)->auth();
    });

    $router->delete('/', function($params){
        $request = new Requests;
        $request->options($params)->auth(1,2,3,4);
    });

    $router->post('/', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->options($params)->auth(1,2,3,4);
    });

    $router->put('/', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->options($params)->auth(1,2,3,4);
    });
});

$router->group('/api/records/{key}', function () use ($router) {
    $router->get('/', function($params){
        $request = new Requests;
        $request->records($params)->auth();
    });

    $router->delete('/', function($params){
        $request = new Requests;
        $request->records($params)->auth(1,2,3,4);
    });

    $router->post('/', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->records($params)->auth(1,2,3,4);
    });

    $router->put('/', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->records($params)->auth(1,2,3,4);
    });
});

$router->group('/api/config', function () use ($router) {
    $router->post('/{key}', function($params){
        $request = new Requests;
        $request->evoAction($params)->auth()->execute(function() use ($params){
            $config = new \EvoPhp\Api\Config();
            return $config->{$params['key']};
        });
    });

    $router->post('/all', function($params){
        $request = new Requests;
        $request->evoAction($params)->auth()->execute(function() use ($params){
            return new \EvoPhp\Api\Config();
        });
    });

    //setter
    $router->post('/', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->evoAction($params)->auth(1,2,3,4)->execute(function() use ($params){
            $config = new \EvoPhp\Api\Config();
            return $config->setMultiple($params);
        });
    });
});

$router->group('/filepond/', function () use ($router) {
    $router->post('/process', function(){
        $request = new Requests;
        $session = Session::getInstance();
        $location = ($r = $session->getResourceOwner()) ? "User/$r->user_id" : "Guest";
        $request->evoAction()->auth(1,2,3,4,5,6,7,8,9)->execute(function() use ($location) {
            $data = reset($_FILES);
            if(!$data) {
                http_response_code(400);
                return 'Empty upload';
            }
            return (new \EvoPhp\Api\FileHandling\Files)->processFile([
                "data" => $data,
                "path" => "Uploads/$location/",
                "saveAs" => "",
                "processor" => "UploadFile"
            ]);
        });
    });

    $router->post('/revert', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->evoAction()->auth(1,2,3,4,5,6,7,8,9)->execute(function() use ($params) {
            $data = reset($params);
            (new \EvoPhp\Api\FileHandling\Files)->unlink($data);
            return $data;
        });
    });
});

$router->get('/login-as/{id}', function($params){
    $user = new \EvoPhp\Resources\User();
    $meta = $user->get((int) $params['id']);
    if($meta == NULL) {
        header("Location: /logout");
    }
    \EvoPhp\Resources\User::pushLogin($meta);
    $index = Operations::getIndex($meta, false);
    header("Location: $index");
});

$router->post('/cross-origin/login', function(){
    $user = new \EvoPhp\Resources\User();
    $resp = $user::corsLogin($_POST);
    if(!$resp['loginStatus']) {
        header("Location: /logout");
    }
    header("Location: ".($resp['index'] ?? "/"));
});

$router->post('/api/login', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth()->execute(function() use ($params){
        return \EvoPhp\Api\Operations::signIn($params['email'], $params['password']);
    });
});

$router->post('/api/loginStatus', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth()->execute(function() use ($params){
        $response = \EvoPhp\Api\Operations::isSignedIn();
        return $response;
    });
});

$router->group('/api/recover-password/', function () use ($router) {
    $router->post('/check-email', function(){
        $request = new Requests;
        $params = (array) json_decode(file_get_contents('php://input'), true);
        $request->evoAction($params)->auth()->execute(function() use ($params){
            $user = new \EvoPhp\Resources\User;
            $meta = $user->get($params['email']);
            if($meta == NULL) {
                http_response_code(400);
                return "E-mail is not linked to any profile";
            }
            $message = "Your verification code for password recovery initiated is ".$params['code'];
            $message = \EvoPhp\Api\Operations::applyFilters("recover_password_message", $message, $params['code']);
            $not = new \EvoPhp\Actions\Notifications\Notifications($message, "Password Recovery");
            $not->to($meta)->template()->mail();
            if($not->error !== "") {
                http_response_code(400);
                return $not->error;
            }
            return $meta->id;
        });
    });

    $router->post('/change', function(){
        $request = new Requests;
        $params = (array) json_decode(file_get_contents('php://input'), true);
        $request->evoAction($params)->auth()->execute(function() use ($params){
            $user = new \EvoPhp\Resources\User;
            $user->update((int) $params['id'], [
                "password" => $params["password"]
            ]);
        });
    });
});

$router->post('/api/doaction', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth()->execute(function() use ($params){
        $action = new \EvoPhp\Actions\Action();
        return $action::do($params['data']['action'], $params['data']['args']);
    });
});

$router->post('/api/change-password', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth(1,2,3,4,5,6,7,8,9)->execute(function() use ($params){
        return (new \EvoPhp\Resources\User())->changePassword($params);
    });
});

$router->post('/api/change-user-password', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth(1,2,3,4,5,6,7,8,9)->execute(function() use ($params){
        return (new \EvoPhp\Resources\User())->changeUserPassword($params);
    });
});

$router->post('/api/index', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth(1,2,3,4,5,6,7,8,9)->execute(function() use ($params){
        return \EvoPhp\Api\Operations::getIndex();
    });
});

$router->post('/api/send-email', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth(1,2,3,4)->execute(function() use ($params){
        if(strpos($params['emails'], ',')) {
            $params['emails'] = \EvoPhp\Api\Operations::trimArray(\explode(',', $params['emails']));
        }
        $not = new \EvoPhp\Actions\Notifications\Notifications($params['message'], $params['subject']);
        $not->to($params['emails'])->template()->mail();
        return $not->error;
    });
});

$router->post('/api/newkey', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth(1,2)->execute(function() use ($request, $params){
        $config = new EvoPhp\Api\Config();
        $exp = new DateTime($params['expiry'], new DateTimeZone($config->timezone));
        $payload = [
            'iss' => $config->root,
            'aud' => $config->root,
            'iat' => time(),
            'nbf' => "1357000000",
            'exp' => $exp->getTimestamp()
        ];
        return Firebase\JWT\JWT::encode($payload, $config->Auth['publickey'] ?? 'apikey', 'HS256');
    });
});

//Tests user access by password
$router->post('/api/test-access', function($params){
    $request = new Requests;
    $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
    $request->evoAction()->auth(1,2,3,4,5,6,7,8,9)->execute(function() use ($params) {
        return \EvoPhp\Api\Operations::testAccess($params['password']);
    });
});

// Main ROUTES
$router->get('/', function(){
    $controller = new MainController;
    $controller->{'Main/index'}()->auth(1,2,3,4,5,6,7,8,9,10)->setData(['pageTitle' => "Profile Control"]);
});

$router->get('/accounts', function($params){
    $controller = new MainController;
    $controller::signOut();
    $controller->{'Accounts/index'}($params)->auth()->template("login")->setData(['pageTitle' => "Login"]);
}); 

$router->get('/admin', function($params){
    $controller = new MainController;
    $controller->{'Admin/index'}($params)->auth(1,2,3,4)->setData(['pageTitle' => "Admin Control"]);
}); 

$router->get('/logout', function(){
    $controller = new MainController;
    $controller->logout()->auth()->template("logout")->setData(['pageTitle' => "Log out"]);
});

$router->get('/test-template/{template}', function($params){
    $controller = new MainController;
    $params = array_merge($params, $_GET);
    $controller->testTemplate($params)->auth(1)->template("no_theme")->setData(['pageTitle' => "Template - ".$params['template']]);
});

$router->get('/migrate-users', function(){
    \EvoPhp\Resources\MigrateUser::migrate();
});
?>