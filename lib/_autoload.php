<?php



/**
 * Autoload function for simpleSAMLphp.
 *
 * It will autoload all classes stored in the lib-directory.
 *
 * @param $className  The name of the class.
 */
function RTT_autoload($className) {

	$libDir = dirname(__FILE__) . '/';

	/* Handlig of modules. */
	if(substr($className, 0, 7) === 'RTTmod_') {
		$modNameEnd = strpos($className, '_', 7);
		$module = substr($className, 7, $modNameEnd - 7);
		$moduleClass = substr($className, $modNameEnd + 1);
		
		$file = RTT_Module::getModuleDir($module) . '/lib/' . str_replace('_', '/', $moduleClass) . '.php';
	} else {
		$file = $libDir . str_replace('_', '/', $className) . '.php';
	}
	
	if(file_exists($file)) {
		require_once($file);
	}
}

/* Register autoload function for simpleSAMLphp. */
if(function_exists('spl_autoload_register')) {
	/* Use the spl_autoload_register function if it is available. It should be available
	 * for PHP versions >= 5.1.2.
	 */
	spl_autoload_register('RTT_autoload');
} else {

	/* spl_autoload_register is unavailable - let us hope that no one else uses the __autoload function. */

	/**
	 * Autoload function for those who don't have spl_autoload_register.
	 *
	 * @param $className  The name of the requested class.
	 */
	function __autoload($className) {
		RTT_autoload($className);
	}
}

?>