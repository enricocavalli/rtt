<?php

/**
 * Configuration utilites - copied from SimpleSAMLphp
 *
 * @author Andreas Aakre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 */
class RTT_Configuration {

	/**
	 * A default value which means that the given option is required.
	 */
	const REQUIRED_OPTION = '___REQUIRED_OPTION___';


	/**
	 * Associative array with mappings from instance-names to configuration objects.
	 */
	private static $instance = array();


	/**
	 * Configration directories.
	 *
	 * This associative array contains the mappings from configuration sets to
	 * configuration directories.
	 */
	private static $configDirs = array();


	/**
	 * Cache of loaded configuration files.
	 *
	 * The index in the array is the full path to the file.
	 */
	private static $loadedConfigs = array();


	/**
	 * The configuration array.
	 */
	private $configuration;


	/**
	 * The location which will be given when an error occurs.
	 */
	private $location;


	/**
	 * The file this configuration was loaded from.
	 */
	private $filename = NULL;



	/**
	 * Initializes a configuration from the given array.
	 *
	 * @param array $config  The configuration array.
	 * @param string $location  The location which will be given when an error occurs.
	 */
	public function __construct($config, $location) {
		assert('is_array($config)');
		assert('is_string($location)');

		$this->configuration = $config;
		$this->location = $location;
	}


	/**
	 * Load the given configuration file.
	 *
	 * @param string $filename  The full path of the configuration file.
	 * @param bool @required  Whether the file is required.
	 * @return SimpleSAML_Configuration  The configuration file. An exception will be thrown if the
	 *                                   configuration file is missing.
	 */
	private static function loadFromFile($filename, $required) {
		assert('is_string($filename)');
		assert('is_bool($required)');

		if (array_key_exists($filename, self::$loadedConfigs)) {
			return self::$loadedConfigs[$filename];
		}

		if (file_exists($filename)) {
			$config = 'UNINITIALIZED';

			/* The file initializes a variable named '$config'. */
			require($filename);

			/* Check that $config is initialized to an array. */
			if (!is_array($config)) {
				throw new Exception('Invalid configuration file: ' . $filename);
			}

		} elseif ($required) {
			/* File does not exist, but is required. */
			throw new Exception('Missing configuration file: ' . $filename);

		} else {
			/* File does not exist, but is optional. */
			$config = array();
		}

		$cfg = new RTT_Configuration($config, $filename);
		$cfg->filename = $filename;

		self::$loadedConfigs[$filename] = $cfg;

		return $cfg;
	}


	/**
	 * Set the directory for configuration files for the given configuration set.
	 *
	 * @param string $path  The directory which contains the configuration files.
	 * @param string $configSet  The configuration set. Defaults to 'rtt'.
	 */
	public static function setConfigDir($path, $configSet = 'rtt') {
		assert('is_string($path)');
		assert('is_string($configSet)');

		self::$configDirs[$configSet] = $path;
	}


	/**
	 * Load a configuration file from a configuration set.
	 *
	 * @param string $filename  The name of the configuration file.
	 * @param string $configSet  The configuration set. Optional, defaults to 'rtt'.
	 */
	public static function getConfig($filename = 'config.php', $configSet = 'rtt') {
		assert('is_string($filename)');
		assert('is_string($configSet)');

		if (!array_key_exists($configSet, self::$configDirs)) {
			if ($configSet !== 'rtt') {
				throw new Exception('Configuration set \'' . $configSet . '\' not initialized.');
			} else {
				self::$configDirs['rtt'] = dirname(dirname(dirname(__FILE__))) . '/config';
			}
		}

		$dir = self::$configDirs[$configSet];
		$filePath = $dir . '/' . $filename;
		return self::loadFromFile($filePath, TRUE);
	}

	/**
	 * Get a configuration file by its instance name.
	 *
	 * This function retrieves a configuration file by its instance name. The instance
	 * name is initialized by the init function, or by copyFromBase function.
	 *
	 * If no configuration file with the given instance name is found, an exception will
	 * be thrown.
	 *
	 * @param string $instancename  The instance name of the configuration file. Depreceated.
	 * @return SimpleSAML_Configuration  The configuration object.
	 */
	public static function getInstance($instancename = 'rtt') {
		assert('is_string($instancename)');

		if ($instancename === 'rtt') {
			return self::getConfig();
		}

		if (!array_key_exists($instancename, self::$instance))
			throw new Exception('Configuration with name ' . $instancename . ' is not initialized.');
		return self::$instance[$instancename];
	}


	/**
	 * Initialize a instance name with the given configuration file.
	 *
	 * @see setConfigDir()
	 * @depreceated  This function is superseeded by the setConfigDir function.
	 */
	public static function init($path, $instancename = 'rtt', $configfilename = 'config.php') {
		assert('is_string($path)');
		assert('is_string($instancename)');
		assert('is_string($configfilename)');

		if ($instancename === 'rtt') {
			/* For backwards compatibility. */
			self::setConfigDir($path, 'rtt');
		}

		/* Check if we already have loaded the given config - return the existing instance if we have. */
		if(array_key_exists($instancename, self::$instance)) {
			return self::$instance[$instancename];
		}

		self::$instance[$instancename] = self::loadFromFile($path . '/' . $configfilename, TRUE);
	}


	/**
	 * Load a configuration file which is located in the same directory as this configuration file.
	 *
	 * @see getConfig()
	 * @depreceated  This function is superseeded by the getConfig() function.
	 */
	public function copyFromBase($instancename, $filename) {
		assert('is_string($instancename)');
		assert('is_string($filename)');
		assert('$this->filename !== NULL');

		/* Check if we already have loaded the given config - return the existing instance if we have. */
		if(array_key_exists($instancename, self::$instance)) {
			return self::$instance[$instancename];
		}

		$dir = dirname($this->filename);

		if ($instancename === 'rtt') {
			/* For backwards compatibility. */
			self::setConfigDir($path, 'rtt');
		}

		self::$instance[$instancename] = self::loadFromFile($dir . '/' . $filename, TRUE);
		return self::$instance[$instancename];
	}


	/**
	 * Retrieve a configuration option set in config.php.
	 *
	 * @param $name  Name of the configuration option.
	 * @param $default  Default value of the configuration option. This parameter will default to NULL if not
	 *                  specified. This can be set to SimpleSAML_Configuration::REQUIRED_OPTION, which will
	 *                  cause an exception to be thrown if the option isn't found.
	 * @return  The configuration option with name $name, or $default if the option was not found.
	 */
	public function getValue($name, $default = NULL) {

		/* Return the default value if the option is unset. */
		if (!array_key_exists($name, $this->configuration)) {
			if($default === self::REQUIRED_OPTION) {
				throw new Exception($this->location . ': Could not retrieve the required option ' .
						var_export($name, TRUE));
			}
			return $default;
		}

		return $this->configuration[$name];
	}


	/**
	 * Retrieve the absolute path of the simpleSAMLphp installation,
	 * relative to the root of the website.
	 *
	 * For example: rtt/
	 *
	 * The path will always end with a '/' and never have a leading slash.
	 *
	 * @return string  The absolute path relative to the root of the website.
	 */
	public function getBaseURL() {
		$baseURL = $this->getString('baseurlpath', 'rtt/');

		if (preg_match('/^\*(.*)$/D', $baseURL, $matches)) {
			/* deprecated behaviour, will be removed in the future */
			return SimpleSAML_Utilities::getFirstPathElement(false) . $matches[1];
		}

		if (preg_match('#^https?://[^/]*/(.*)$#', $baseURL, $matches)) {
			/* we have a full url, we need to strip the path */
			return $matches[1];
		} elseif ($baseURL === '' || $baseURL === '/') {
			/* Root directory of site. */
			return '';
		} elseif (preg_match('#^/?([^/]?.*/)#D', $baseURL, $matches)) {
			/* local path only */
			return $matches[1];
		} else {
			/* invalid format */
			throw new Exception('Incorrect format for option \'baseurlpath\'. Value is: "'.
					$this->getString('baseurlpath', 'rtt/') . '". Valid format is in the form'.
					' [(http|https)://(hostname|fqdn)[:port]]/[path/to/rtt/].');
		}
	}

	/**
	 * Retrieve the base directory for this simpleSAMLphp installation.
	 * This function first checks the 'basedir' configuration option. If
	 * this option is undefined or NULL, then we fall back to looking at
	 * the current filename.
	 *
	 * @return The absolute path to the base directory for this simpleSAMLphp
	 *  installation. This path will always end with a slash.
	 */
	public function getBaseDir() {
		/* Check if a directory is configured in the configuration
		 * file.
		*/
		$dir = $this->getString('basedir', NULL);
		if($dir !== NULL) {
			/* Add trailing slash if it is missing. */
			if(substr($dir, -1) !== '/') {
				$dir .= '/';
			}

			return $dir;
		}

		/* The directory wasn't set in the configuration file. Our
		 * path is <base directory>/lib/SimpleSAML/Configuration.php
		*/

		$dir = __FILE__;
		assert('basename($dir) === "Configuration.php"');

		$dir = dirname($dir);
		assert('basename($dir) === "RTT"');

		$dir = dirname($dir);
		assert('basename($dir) === "lib"');

		$dir = dirname($dir);

		/* Add trailing slash. */
		$dir .= '/';

		return $dir;
	}


	/**
	 * This function retrieves a boolean configuration option.
	 *
	 * An exception will be thrown if this option isn't a boolean, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param $name  The name of the option.
	 * @param $default  A default value which will be returned if the option isn't found. The option will be
	 *                  required if this parameter isn't given. The default value can be any value, including
	 *                  NULL.
	 * @return  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getBoolean($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			*/
			return $ret;
		}

		if(!is_bool($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
					' is not a valid boolean value.');
		}

		return $ret;
	}


	/**
	 * This function retrieves a string configuration option.
	 *
	 * An exception will be thrown if this option isn't a string, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param $name  The name of the option.
	 * @param $default  A default value which will be returned if the option isn't found. The option will be
	 *                  required if this parameter isn't given. The default value can be any value, including
	 *                  NULL.
	 * @return  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getString($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			*/
			return $ret;
		}

		if(!is_string($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
					' is not a valid string value.');
		}

		return $ret;
	}


	/**
	 * This function retrieves an integer configuration option.
	 *
	 * An exception will be thrown if this option isn't an integer, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param $name  The name of the option.
	 * @param $default  A default value which will be returned if the option isn't found. The option will be
	 *                  required if this parameter isn't given. The default value can be any value, including
	 *                  NULL.
	 * @return  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getInteger($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			*/
			return $ret;
		}

		if(!is_int($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
					' is not a valid integer value.');
		}

		return $ret;
	}

	/**
	 * This function retrieves an array configuration option.
	 *
	 * An exception will be thrown if this option isn't an array, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param string $name  The name of the option.
	 * @param mixed$default  A default value which will be returned if the option isn't found. The option will be
	 *                       required if this parameter isn't given. The default value can be any value, including
	 *                       NULL.
	 * @return mixed  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getArray($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if ($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			*/
			return $ret;
		}

		if (!is_array($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
					' is not an array.');
		}

		return $ret;
	}

}
