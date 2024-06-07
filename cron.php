<?php

if ( !defined('ABSPATH') )
	define('ABSPATH', realpath(dirname(__FILE__)) . '/');

require_once("EvoPhp/autoload.php");

\EvoPhp\Resources\Records::update("cronjobsonline", true);

\EvoPhp\Api\Cron::executeDueJobs();
?>