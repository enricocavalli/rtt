<?php
require_once('_include.php');

$m = new RTT_StoreMeasure();

print '<pre>';
print_r($m->getMeasure());