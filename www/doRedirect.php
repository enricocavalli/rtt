<?php
/*
 * this is an example of how to use the results of measurement
* to be used outside of this program you will use
* require_once('full/path/to/rtt/www/_include.php');
*/

require_once('_include.php');

$site=RTT_Measure::getSite();
// TODO - do whatever with site
print_r($site);