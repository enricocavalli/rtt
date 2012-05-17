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

	function getProbeURL($site) {

		$ret=NULL;

		if (isset($this->probe_sites[$site]['probeurl'])) {
			$ret=$this->probe_sites[$site]['probeurl'];
		}

		return $ret;
	}

	function getSiteTimeout($site, $default_timeout=3) {

		$ret=$default_timeout;
		if (isset($this->probe_sites[$site]['timeout'])) {
			$ret=$this->probe_sites[$site]['timeout'];
		}

		return $ret;

	}

	/**
	 * returns an array with siteNames
	 *
	 * @return array
	 *
	 */

	public static function getSiteNames() {
		
		$config=RTT_Configuration::getInstance();
		return array_keys($config->getArray('sites'));
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
	 * Records probe data with the RTT_StoreMeasure class
	 *
	 * @param string $site
	 * @param string $rtt
	 */
	function recordMeasure($site,$rtt) {

		if (array_key_exists($site, $this->probe_sites) && preg_match('/[0-9]+/', $rtt)) {
			$store= new RTT_StoreMeasure();
			$store->addMeasure($site, $rtt);
		} else {
			throw new RTT_Exception('Site or rtt have incorrect format');
		}


	}

	/**
	 * Get the best site from previuous measures.
	 * If no measure is present returns random site.
	 *
	 * @return string site
	 */

	public static function getSite() {
		$store = new RTT_StoreMeasure();

		$measures=$store->getMeasureAsArray();

		if (count($measures)>0) {
			$min_value=min($measures);

			foreach($measures as $site_id => $value) {
				if($value == $min_value)  $min_key=$site_id;
			}
		}

		if(!isset($min_key)) {

			$sites = self::getSiteNames();
			$index = array_rand($sites);
			$min_key=$sites[$index];
		}

		return $min_key;

	}

}