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
// $apiKey = 'Cmjtd%7Cluur2108n1,7w=o5-gz8a';
// $url = "https://www.mapquestapi.com/geocoding/v1/address?key=$apiKey&location=Umuchigbo+Nike,+Enugu,+Nigeria";
// var_dump(\EvoPhp\Api\Operations::callAPI("GET", $url, [])->results);
// \EvoPhp\Api\Cron::schedule("* * * 5 1", "\EvoPhp\Api\Cron::testCb", 12, "okay");
\EvoPhp\Api\Cron::executeDueJobs();
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>