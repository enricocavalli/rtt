<?php
require_once('_include.php');

$m = new RTT_Measure();
$config = RTT_Configuration::getInstance();

foreach( $m->getSiteNames() as $site ) {

	$url = RTT_Utilities::getBaseURL() . 'singleMeasure.php?site=' . $site;
	echo '<iframe src="' . $url . '" style="display:none"></iframe>';

}
