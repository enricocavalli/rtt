<?php

$m = new RTTmod_measure_Measure();
$config = RTT_Configuration::getInstance();
foreach( $m->getSiteNames() as $site ) {
    
	
	$url = RTT_Utilities::getBaseURL() . 'index.php/measure/singleMeasure.php?site=' . $site;
	echo '<iframe src="' . $url . '" style="display:none"></iframe>';
	
}