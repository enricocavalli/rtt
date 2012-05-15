<?php
$config = array(
		//'baseurlpath' => 'http://mirror.garr.it/rtt/',
		'cookie.path' => '/',
		'cookie.domain' => '.mirror.garr.it',
		'cookie.name' => 'RTTmirror',
		'cookie.lifetime' => 3600,
		
		
		'sites' => array (
			'mi' => array(
						'probeurl' => 'http://rttmi.mirror.garr.it/rtt/',
						'timeout' => 3,
						),
			'rm' => array (
						'probeurl' => 'http://rttrm.mirror.garr.it/rtt/',
						'timeout' => 5,
					),
				),
		

		
	);