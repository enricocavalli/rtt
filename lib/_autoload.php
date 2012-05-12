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

	$file = $libDir . str_replace('_', '/', $className) . '.php';
	
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