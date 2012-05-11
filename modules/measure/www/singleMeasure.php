<?php

	if (!array_key_exists('site', $_REQUEST)) {
		throw  new Exception('Site obbligatorio');
	}
	
	$site=$_REQUEST['site'];
	$m= new RTTmod_measure_Measure();
	
	if (array_key_exists('rtt', $_REQUEST)) {

		$rtt = $_REQUEST['rtt'];
		$m->recordMeasure($site, $rtt);
	 
	} else {
	
		$m->probeSingleSite($site);
	
	}