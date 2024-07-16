<?php
if(!file_exists("EvoPhp/Database/Config.php")) {
	header("Location: \Install.php");
} 

if (preg_match('/\.(?:png|jpg|jpeg|css|js|woff|woff2)$/', $_SERVER['REQUEST_URI'])) {
    return false;
}

if ( !defined('ABSPATH') )
	define('ABSPATH', realpath(dirname(__FILE__)) . '/');

require_once("EvoPhp/autoload.php");

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
// var_dump((new \Public\Modules\eEdu\Classes\Exams)->getBroadsheet(["exam_id" => 5]));
?>