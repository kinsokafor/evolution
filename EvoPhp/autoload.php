<?php
	use EvoPhp\Api\Modules;
	use EvoPhp\Api\Operations;
	use EvoPhp\Api\EvoRouter;

	spl_autoload_register(function($className)
	{
		$className = str_replace('\\', '/', $className);
		$file = ABSPATH."$className.php";
		if(file_exists($file)) include $file;
	});

	// load dependency files
	require ABSPATH.'vendor/autoload.php';

	// load modules
	$modules = Modules::getModules();
	$router = new EvoRouter();
	if(Operations::count($modules)) {
		foreach($modules as $moduleName => $module) {
			if(Modules::moduleActive($module)) {
				$file = Modules::getModulePath($module)."Routes.php";
				if(file_exists($file)) include $file;
			}
		}
	}
?>