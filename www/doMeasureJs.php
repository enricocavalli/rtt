<?php
require_once('_include.php');
echo '<html>
<head>
  <style type="text/css">
   .hidden { display:none }
  </style>
</head>
<body>
<script type="text/javascript" src="js/jquery.js"></script>
';


$m = new RTT_Measure();
$config = RTT_Configuration::getInstance();
	echo '<div id="measure-iframes">';
foreach( $m->getSiteNames() as $site ) {
	
	$siteTimeouts[$site]=$m->getSiteTimeout($site);
	$siteStatus[$site]='inprogress';
	$url = RTT_Utilities::getBaseURL() . 'singleMeasure.php?site=' . $site;
	echo '<iframe src="' . $url . '" style="display:none"></iframe>';
	
	echo '<div id="rtt-'.$site.'">'.$site.' <img id="statusimage-'.$site.'" src="icons/progress.gif"></div>';	
	
}
echo '</div>';

echo '
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
";

echo '

function printMeasure(data) {

	$.each(data,function(index,value) { $("#output").append(index+" : "+value+"<br/>").show("slow"); }	);
	$("#measure-iframes").hide("slow");
}
</script>
<div id="output" class="hidden"></div>
</body>
</html>
';