<?php
	use EvoPhp\Api\Modules;
	use EvoPhp\Api\Operations;
	use EvoPhp\Api\EvoRouter;
	use EvoPhp\Api\Config;

	spl_autoload_register(function($className)
	{
		$className = str_replace('\\', '/', $className);
		$file = ABSPATH."$className.php";
		if(file_exists($file)) include $file;
	});

	// load dependency files
	require ABSPATH.'vendor/autoload.php';

	date_default_timezone_set((new Config)->timezone ?? "UTC");

	// load modules
	$modules = Modules::getModules();
	$router = new EvoRouter();
	if(Operations::count($modules)) {
		foreach($modules as $moduleName => $module) {
			if(Modules::moduleActive($module)) {
				$file = Modules::getModulePath($moduleName)."Routes.php";
				if(file_exists($file)) include $file;
			}
		}
	}
	if(file_exists("Database/Config.php")) {
		if(\EvoPhp\Resources\Records::get("cronjobsonline") !== true) {
			\EvoPhp\Api\Cron::executeDueJobs(5);
		}
	}

	if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		$allowedOrigins = [
			\EvoPhp\Api\Operations::fullProtocol()
		];
		
		// Get the Origin header from the client request
		$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

		\Delight\Http\ResponseHeader::set('Access-Control-Allow-Origin', $origin);
		\Delight\Http\ResponseHeader::set('Access-Control-Allow-Methods', 'OPTIONS,GET,POST,PUT,DELETE');
		\Delight\Http\ResponseHeader::set('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
		\Delight\Http\ResponseHeader::set('Access-Control-Allow-Credentials', true);
	}
?>