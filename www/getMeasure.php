<?php
require_once('_include.php');

header('Content-type: application/json');
$m = new RTT_StoreMeasure();
echo $m->getMeasure();