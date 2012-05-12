<?php
class RTT_Measure {
	
	private $probe_sites;	
	
	public function __construct() {
		$config=RTT_Configuration::getInstance();
		$this->probe_sites=$config->getArray('sites');	
	}
	
	/**
	 * Get the probe url of a given site
	 * 
	 * @param string $site
	 */
	
	private function getProbeURL($site) {
		
		$ret=NULL;
		
		if (isset($this->probe_sites[$site]['probeurl'])) {
			$ret=$this->probe_sites[$site]['probeurl'];
		}		
		
		return $ret;
	}
	
	/**
	 * returns an array with siteNames
	 * 
	 * @return array
	 * 
	 */
	
	public function getSiteNames() {
		return array_keys($this->probe_sites);
	}
	
	/**
	 * Probes a single site redirecting to the configured url
	 * 
	 * @param string $sitename
	 */
	
	function probeSingleSite($sitename) {
		if( array_key_exists($sitename, $this->probe_sites) ) {
			$url=self::getProbeURL($sitename);
			RTT_Utilities::redirect($url);
		}

	}
	
	
	/**
	 * Records probe data in cookie
	 * 
	 * @param string $site
	 * @param string $rtt
	 */
	function recordMeasure($site,$rtt) {
		
		$store= new RTT_StoreMeasure();
		$store->addMeasure($site, $rtt);
		$store->storeMeasure();
		

	}
	
}