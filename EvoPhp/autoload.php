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
?>