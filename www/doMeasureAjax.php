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
foreach( $m->getSiteNames() as $site ) {
	
	$url = RTT_Utilities::getBaseURL() . 'singleMeasure.php?site=' . $site;
	echo '<iframe src="' . $url . '" style="display:none"></iframe>';
	
}

echo '
<script type="text/javascript">
$.ajax({
	type: "GET",
	url: "getMeasure.php",
	dataType: "json",
	success: function(response) {
	validateMeasure(response);
	}
});

function validateMeasure(data) {

if (data != \'\') {
$.each(data,function(index,value) { $("#output").append(index+" : "+value+"<br/>").show("slow"); }	);
}
}
</script>
<div id="output" class="hidden"></div>
</body>
</html>
';