<?php
require_once('_include.php');

try {

	if (!array_key_exists('site', $_REQUEST)) {
		throw  new RTT_Exception('Site obbligatorio');
	}

	$site=$_REQUEST['site'];
	$m= new RTT_Measure();

	if (array_key_exists('rtt', $_REQUEST)) {
		$rtt = $_REQUEST['rtt'];
		$m->recordMeasure($site, $rtt);
		echo '
		<!DOCTYPE html>
		<html>
		<script>
		if(window.parent.js==true) {
		window.parent.measureCompleted("'. $site .'","'. $rtt .'");
	}
	</script>
	</html>
	';
	} else {

		$m->probeSingleSite($site);

	}
} catch (RTT_Exception $e) {
	print $e->getMessage();
}