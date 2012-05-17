<?php

/*
 * this class stores measures with cookies on client side.
*/

class RTT_StoreMeasure {

	private $base_cookie_name;
	private $site_names=array();
	private $measure=array();

	public function __construct() {
		$config=RTT_Configuration::getInstance();
		$this->base_cookie_name=$config->getString('cookie.name','RTTmirror');
		$this->site_names = array_keys($config->getArray('sites'));
		$this->measure = self::getPreviousMeasure();
	}

	private function getPreviousMeasure() {

		$ret=array(); 
		
		foreach ($this->site_names as $s) {
			if(array_key_exists($this->base_cookie_name.'-'.$s, $_COOKIE)) {
				$ret[$s]=$_COOKIE[$this->base_cookie_name.'-'.$s];
			}
		}

		return $ret;

	}

	/**
	 * get a Measure and returns it as JSON
	 *
	 * @return string JSON data representing measures
	 */

	function getMeasure() {
		return json_encode($this->measure);
	}

	/**
	 * returns the array with measures - associative array site => measure
	 *
	 * @return array
	 */

	function getMeasureAsArray() {

		return $this->measure;
	}
	
	function addMeasure($site,$rtt) {

		$this->measure[$site]=$rtt;
		$this->storeMeasure();

	}

	private function storeMeasure() {
		$cookie_params = RTT_Utilities::getCookieParams();
		
		foreach ($this->measure as $k => $v) {
		setcookie($this->base_cookie_name.'-'.$k,$v,time() + $cookie_params['lifetime'],
				$cookie_params['path'],$cookie_params['domain'],$cookie_params['secure'],$cookie_params['httponly']);
		}
	}

}