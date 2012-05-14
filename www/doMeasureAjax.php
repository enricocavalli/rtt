<?php
require_once('_include.php');
echo '<html>
<script type="text/javascript" src="js/jquery.js"></script>';
$m = new RTT_Measure();
$config = RTT_Configuration::getInstance();
foreach( $m->getSiteNames() as $site ) {
	
	$url = RTT_Utilities::getBaseURL() . 'singleMeasure.php?site=' . $site;
	echo '<iframe src="' . $url . '" style="display:none"></iframe>';
	
}

echo '
<p><button id="b1">Run</button></p>
<script type="text/javascript">
$("#b1").click(function() {
$.ajax({
	type: "GET",
	url: "getMeasure.php",
	dataType: "json",
	success: function(response) {
	$.each(response,function(index,value) { $("#output").append(" "+index+":"+value).css(\'opacity\',\'0\').animate({ opacity: 1 },1000); }	);
	}
});
});

</script>
<div id="output" style="opacity: 0"></div>
</html>
';