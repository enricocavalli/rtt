<?php
require_once('_include.php');

header('Content-type: application/json');
echo RTT_Measure::getMeasure();