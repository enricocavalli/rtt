<?php
require_once('_include.php');

foreach( RTT_Measure::getSiteNames() as $site ) {

	$url = RTT_Utilities::getBaseURL() . 'singleMeasure.php?site=' . $site;
	echo '<iframe src="' . $url . '" style="display:none"></iframe>';

}
