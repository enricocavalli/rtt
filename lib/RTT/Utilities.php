<?php
/*
 * this class and utilities is copied from simpleSAMLphp
*/
class RTT_Utilities {

	/* This function redirects the user to the specified address.
	 * An optional set of query parameters can be appended by passing
	* them in an array.
	*
	* This function will use the HTTP 303 See Other redirect if the
	* current request is a POST request and the HTTP version is HTTP/1.1.
	* Otherwise a HTTP 302 Found redirect will be used.
	*
	* The fuction will also generate a simple web page with a clickable
	* link to the target page.
	*
	* Parameters:
	*  $url         URL we should redirect to. This URL may include
	*               query parameters. If this URL is a relative URL
	*               (starting with '/'), then it will be turned into an
	*               absolute URL by prefixing it with the absolute URL
	*               to the root of the website.
	*  $parameters  Array with extra query string parameters which should
	*               be appended to the URL. The name of the parameter is
	*               the array index. The value of the parameter is the
	*               value stored in the index. Both the name and the value
	*               will be urlencoded. If the value is NULL, then the
	*               parameter will be encoded as just the name, without a
	*               value.
	*
	* Returns:
	*  This function never returns.
	*/
	public static function redirect($url, $parameters = array()) {
	assert(is_string($url));
	assert(strlen($url) > 0);
	assert(is_array($parameters));

	/* Check for relative URL. */
	if(substr($url, 0, 1) === '/') {
		/* Prefix the URL with the url to the root of the
		 * website.
		*/
		$url = self::selfURLhost() . $url;
	}

	/* Verify that the URL is to a http or https site. */
	if (!preg_match('@^https?://@i', $url)) {
		throw new Exception('Redirect to invalid URL: ' . $url);
	}

	/* Determine which prefix we should put before the first
	 * parameter.
	*/
	if(strpos($url, '?') === FALSE) {
		$paramPrefix = '?';
	} else {
		$paramPrefix = '&';
	}

	/* Iterate over the parameters and append them to the query
	 * string.
	*/
	foreach($parameters as $name => $value) {

		/* Encode the parameter. */
		if($value === NULL) {
			$param = urlencode($name);
		} elseif (is_array($value)) {
			$param = "";
			foreach ($value as $val) {
				$param .= urlencode($name) . "[]=" . urlencode($val) . '&';
			}
		} else {
			$param = urlencode($name) . '=' .
					urlencode($value);
		}

		/* Append the parameter to the query string. */
		$url .= $paramPrefix . $param;

		/* Every following parameter is guaranteed to follow
		 * another parameter. Therefore we use the '&' prefix.
		*/
		$paramPrefix = '&';
	}


	/* Set the HTTP result code. This is either 303 See Other or
	 * 302 Found. HTTP 303 See Other is sent if the HTTP version
	* is HTTP/1.1 and the request type was a POST request.
	*/
	if($_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' &&
			$_SERVER['REQUEST_METHOD'] === 'POST') {
		$code = 303;
	} else {
		$code = 302;
	}

	if (strlen($url) > 2048) {
		SimpleSAML_Logger::warning('Redirecting to URL longer than 2048 bytes.');
	}

	/* Set the location header. */
	header('Location: ' . $url, TRUE, $code);

	/* Disable caching of this response. */
	header('Pragma: no-cache');
	header('Cache-Control: no-cache, must-revalidate');

	/* Show a minimal web page with a clickable link to the URL. */
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' .
			' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml">';
	echo '<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>Redirect</title>
	</head>';
	echo '<body>';
	echo '<h1>Redirect</h1>';
	echo '<p>';
	echo 'You were redirected to: ';
	echo '<a id="redirlink" href="' . htmlspecialchars($url) . '">' . htmlspecialchars($url) . '</a>';
	echo '<script type="text/javascript">document.getElementById("redirlink").focus();</script>';
	echo '</p>';
	echo '</body>';
	echo '</html>';

	/* End script execution. */
	exit;
}


public static function getBaseURL() {

	$globalConfig = RTT_Configuration::getInstance();
	$baseURL = $globalConfig->getString('baseurlpath', 'rtt/');

	if (preg_match('#^https?://.*/$#D', $baseURL, $matches)) {
		/* full url in baseurlpath, override local server values */
		return $baseURL;
	} elseif (
			(preg_match('#^/?([^/]?.*/)$#D', $baseURL, $matches)) ||
			(preg_match('#^\*(.*)/$#D', $baseURL, $matches)) ||
			($baseURL === '')) {
		/* get server values */

		if (self::getServerHTTPS()) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}

		$hostname = self::getServerHost();
		$port = self::getServerPort();
		$path = '/' . $globalConfig->getBaseURL();

		return $protocol.$hostname.$port.$path;
	} else {
		throw new Exception('Invalid value of \'baseurl\' in '.
				'config.php. Valid format is in the form: '.
				'[(http|https)://(hostname|fqdn)[:port]]/[path/to/simplesaml/]. '.
				'It must end with a \'/\'.');
	}

}

/**
 * retrieve HTTPS status from $_SERVER environment variables
 */
private static function getServerHTTPS() {

	if(!array_key_exists('HTTPS', $_SERVER)) {
		/* Not an https-request. */
		return FALSE;
	}

	if($_SERVER['HTTPS'] === 'off') {
		/* IIS with HTTPS off. */
		return FALSE;
	}

	/* Otherwise, HTTPS will be a non-empty string. */
	return $_SERVER['HTTPS'] !== '';

}


/**
 * Retrieve port number from $_SERVER environment variables
 * return it as a string such as ":80" if different from
 * protocol default port, otherwise returns an empty string
 */
private static function getServerPort() {

	$portnumber = $_SERVER["SERVER_PORT"];
	$port = ':' . $portnumber;

	if (self::getServerHTTPS()) {
		if ($portnumber == '443') $port = '';
	} else {
		if ($portnumber == '80') $port = '';
	}

	return $port;

}

/**
 * Retrieve Host value from $_SERVER environment variables
 */
private static function getServerHost() {

	if (array_key_exists('HTTP_HOST', $_SERVER)) {
		$currenthost = $_SERVER['HTTP_HOST'];
	} elseif (array_key_exists('SERVER_NAME', $_SERVER)) {
		$currenthost = $_SERVER['SERVER_NAME'];
	} else {
		/* Almost certainly not what you want, but ... */
		$currenthost = 'localhost';
	}

	if(strstr($currenthost, ":")) {
		$currenthostdecomposed = explode(":", $currenthost);
		$currenthost = $currenthostdecomposed[0];
	}
	return $currenthost;

}



/**
 * Get the cookie parameters that should be used for session cookies.
 *
 * @return array
 * @link http://www.php.net/manual/en/function.session-get-cookie-params.php
 */
public static function getCookieParams() {

	$config = RTT_Configuration::getInstance();

	return array(
			'lifetime' => $config->getInteger('cookie.lifetime', 0),
			'path' => $config->getString('cookie.path', '/'),
			'domain' => $config->getString('cookie.domain', NULL),
			'secure' => $config->getBoolean('cookie.secure', FALSE),
			'httponly' => TRUE,
	);
}

}