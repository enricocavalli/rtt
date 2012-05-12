<?php

require_once('_include.php');

$m = new RTT_Measure();

$site=$m->getSite();

// TODO - do whatever with site
print_r($site);