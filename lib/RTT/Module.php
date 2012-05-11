<?php
class RTT_Module {


	/**
	 * Retrieve the base directory for a module.
	 *
	 * The returned path name will be an absoulte path.
	 *
	 * @param string $module  Name of the module
	 * @return string  The base directory of a module.
	 */
	public static function getModuleDir($module) {
		$baseDir = dirname(dirname(dirname(__FILE__))) . '/modules';
		$moduleDir = $baseDir . '/' . $module;

		return $moduleDir;
	}
	
}