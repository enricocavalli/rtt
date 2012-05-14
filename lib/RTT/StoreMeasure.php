<?php
class RTT_StoreMeasure {
	
	private $cookie_name;
	private $measure=array();
	
	public function __construct() {
		$config=RTT_Configuration::getInstance();
		$this->cookie_name=$config->getString('cookie.name','RTTmirror');
		$this->measure = self::getPreviousMeasure();		
	}
	
	private function getPreviousMeasure() {
		
		$ret=array();
		if (array_key_exists($this->cookie_name,$_COOKIE)) {
			$ret=json_decode($_COOKIE[$this->cookie_name],TRUE);		
		} 
		return $ret;
		
	}
	function getMeasure() {
		return json_encode($this->measure);
	}
	
	function addMeasure($site,$rtt) {
		
		$this->measure[$site]=$rtt;
		
	}
	
	function storeMeasure() {
		$cookie_params = RTT_Utilities::getCookieParams();
			setcookie($this->cookie_name,json_encode($this->measure),time() + $cookie_params['lifetime'],
					$cookie_params['path'],$cookie_params['domain'],$cookie_params['secure'],$cookie_params['httponly']);
	}
	
}