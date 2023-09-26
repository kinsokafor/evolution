<?php  

use Public\Modules\Main\MainController;
use EvoPhp\Api\Requests\Requests;
use EvoPhp\Database\Session;

// API ROUTES
$router->group('/api/user', function () use ($router) {
    $router->get('/', function () {
        $request = new Requests;
        $request->user()->auth(3);
    });
    $router->get('/id/{id}', function ($params) {
        $request = new Requests;
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
        $request->user($params)->auth(1,2,3);
    });
    $router->put('/id/{id}', function ($params) {
        $request = new Requests;
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

$router->group('/api/dbtable/{table}', function () use ($router) {
    $router->get('/', function($params){
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
    $router->post('/{type}', function ($params) {
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

    $router->post('/', function($params){
        $request = new Requests;
        $params = array_merge($params, (array) json_decode(file_get_contents('php://input'), true));
        $request->evoAction($params)->auth(1,2,3,4)->execute(function() use ($params){
            $config = new \EvoPhp\Api\Config();
            return $config->setMultiple($params);
        });
    });
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

// $router->post('/api/config', function(){
//     $request = new Requests;
//     $params = (array) json_decode(file_get_contents('php://input'), true);
//     $request->evoAction($params)->auth()->execute(function() use ($params){
//         return new \EvoPhp\Api\Config();
//     });
// });

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
        return (new \EvoPhp\Resources\user())->changePassword($params);
    });
});

$router->post('/api/change-user-password', function(){
    $request = new Requests;
    $params = (array) json_decode(file_get_contents('php://input'), true);
    $request->evoAction($params)->auth(1,2,3,4,5,6,7,8,9)->execute(function() use ($params){
        return (new \EvoPhp\Resources\user())->changeUserPassword($params);
    });
});

// Main ROUTES
$router->get('/', function(){
    $controller = new MainController;
    $controller->{'Main/index'}()->auth(1,2,3,4,5,6,7,8,9)->setData(['pageTitle' => "Profile Control"]);
});

$router->get('/accounts', function($params){
    $controller = new MainController;
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

?>