<?php

/* Remove magic quotes. */
if(get_magic_quotes_gpc()) {
	foreach(array('_GET', '_POST', '_COOKIE', '_REQUEST') as $a) {
		if (isset($$a) && is_array($$a)) {
			foreach($$a as &$v) {
				/* We don't use array-parameters anywhere.
				 * Ignore any that may appear.
				 */
				if(is_array($v)) {
					continue;
				}
				/* Unescape the string. */
				$v = stripslashes($v);
			}
		}
	}
}
if (get_magic_quotes_runtime()) {
	set_magic_quotes_runtime(FALSE);
}


/* Initialize the autoloader. */
require_once(dirname(dirname(__FILE__)) . '/lib/_autoload.php');


$configdir = dirname(dirname(__FILE__)) . '/config';
if (!file_exists($configdir . '/config.php')) {
	header('Content-Type: text/plain');
	echo("You have not yet created the  configuration files.\n");
	exit(1);
}