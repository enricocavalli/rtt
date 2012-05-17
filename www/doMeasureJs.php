<?php
require_once('_include.php');
echo '
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
	.hidden { display:none }
	.testo {
		font-family:monospace;
		font-size: 10pt;
	}
	.measure-iframes-container {
		float: left;
	}
	.measure-iframes-img {
		float: left;
		padding-right: 10px;
		vertical-align: top;
	}
</style>
</head>
<body>
<script type="text/javascript" src="js/jquery.js"></script>
';


$m = new RTT_Measure();
$config = RTT_Configuration::getInstance();
echo '<div class="measure-iframes-container" id="measure-iframes">';
echo '<div class="measure-iframes-img testo">Connection Information: IP <b>'.$_SERVER['REMOTE_ADDR'].'</b></div>';
echo '<div id="output" class="hidden"></div>';
foreach( $m->getSiteNames() as $site ) {

	$siteTimeouts[$site]=$m->getSiteTimeout($site);
	$siteStatus[$site]='inprogress';
	$url = RTT_Utilities::getBaseURL() . 'singleMeasure.php?site=' . $site;
	echo '<iframe src="' . $url . '" style="display:none"></iframe>';
	echo '<div class="testo measure-iframes-img" id="rtt-'.$site.'">'.$site.' <img id="statusimage-'.$site.'" src="icons/progress.gif"></div>';

}
echo '</div>
<script type="text/javascript">
window.siteTimeouts='. json_encode($siteTimeouts) .'
window.siteStatus='. json_encode($siteStatus).'
window.siteMeasures={}
window.js=true;
';

echo "
function measureCompleted(site,rtt) {

	$('#statusimage-' + site).attr('src', 'icons/accept.png');
	window.siteStatus[site]='completed';
	window.siteMeasures[site]=rtt;
	
}

function measureFailed(site,message) {

	$('#statusimage-' + site).attr('src', 'icons/exclamation.png');
	window.siteStatus[site]='failed';
	
}

function updateStatus() {

	var nFailed = 0;
	var nProgress = 0;
	
	for (s in window.siteStatus) {
	
		switch (window.siteStatus[s]) {
			case 'failed':
				nFailed += 1;
				break;
				
			case 'inprogress':
				nProgress += 1;
				break;
		}
	}

	if (nProgress == 0 && nFailed == 0) {
		$('#measure-completed').show();
		printMeasure(window.siteMeasures);
	} else {
		window.timeoutID = window.setTimeout(timeoutMeasures, 1000);
	}

}

function timeoutMeasures() {

	var cTime = ( (new Date()).getTime() - window.startTime ) / 1000;

	for (s in window.siteTimeouts) {
		if (window.siteTimeouts[s] <= cTime && window.siteStatus[s]!='completed') {
			measureFailed(s, 'Timeout');
		}
	}
	
	updateStatus();
	
}

	$('document').ready(function(){
		window.startTime = (new Date()).getTime();
		updateStatus();
	});

	function printMeasure(data) {
	
		$(\"#output\").attr('class','hidden testo measure-iframes-img');
		$(\"#output\").append(JSON.stringify(data)).show(\"slow\");

	}
</script>
</body>
</html>
";