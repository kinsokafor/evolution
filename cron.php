<?php

if (preg_match('/\.(?:png|jpg|jpeg|css|js|woff|woff2)$/', $_SERVER['REQUEST_URI'] ?? "")) {
    return false;
}

if ( !defined('ABSPATH') )
	define('ABSPATH', realpath(dirname(__FILE__)) . '/');

require_once("EvoPhp/autoload.php");

\EvoPhp\Api\Cron::executeDueJobs();
?>