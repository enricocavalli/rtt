<?php
header('Content-Type: text/html;charset=UTF-8');
require_once('_include.php');

try {

	if (!array_key_exists('site', $_REQUEST)) {
		throw  new RTT_Exception('Site obbligatorio');
	}

	$site=$_REQUEST['site'];
	$m= new RTT_Measure();

	if (array_key_exists('rtt', $_REQUEST)) {
		$rtt = $_REQUEST['rtt'];
		if($rtt>0) {
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
			echo "<!DOCTYPE html><html><script>
			var matches = window.location.search.match(/\?site=([^&]*)&/);
			var site=matches[1];
			var newurl = window.location.protocol+'//'+window.location.host+window.location.pathname+'?site='+site;
			window.location.href=newurl;
			</script></html>";
		}
	} else {

		$m->probeSingleSite($site);

	}
} catch (RTT_Exception $e) {
	print $e->getMessage();
}