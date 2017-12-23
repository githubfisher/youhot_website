<?php
	$config['A'] = array(
                'weight' => 0.5,
                'first'  => 52,
                'per'    => 26,
                'per_weight' => 0.5,
                'currency' => 'RMB',
                'count_unit' => 'KG'
        );
        $config['B'] = array(
                'weight' => 2,
                'first'  => 76,
                'per'    => 38,
                'per_weight' => 1,
                'currency' => 'RMB',
                'count_unit' => 'LBS'
        );
        $config['C'] = array(
                'weight' => 0.5,
                'first'  => 55,
                'per'    => 15,
                'per_weight' => 0.5,
                'currency' => 'RMB',
                'count_unit' => 'KG'
        );
        $config['D'] = array(
                'weight' => 0.1,
                'first'  => 20,
                'per'    => 2,
                'per_weight' => 0.1,
                'currency' => 'RMB',
                'count_unit' => 'KG'
        );
        $config['E'] = array(
                'weight' => 0.1,
                'first'  => 15,
                'per'    => 1.5,
                'per_weight' => 0.1,
                'currency' => 'RMB',
		'count_unit' => 'KG'
        );
	$config['bs'] = array( // C
		'weight' => 2,
		'first'  => 50,
		'per'    => 25,
		'per_weight' => 1,
		'days'   => '0-0',
                'currency' => 'USD',
		'count_unit' => 'LBS'
	);
	$config['UE'] = array( // USA EMS B
		'weight' => 1,
		'first'  => 15,
		'per'    => 5,
		'per_weight' => 1,
		'days'   => '0-0',
		'currency' => 'USD',
		'count_unit' => 'LBS'
	);
	$config['ems'] = array( // EMS  A
                'weight' => 0.5,
                'first'  => 45,
                'per'    => 20,
                'per_weight' => 0.5,
                'days'   => '0-0',
                'currency' => 'RMB',
                'count_unit' => 'KG'
        );
	$config['fedex'] = array( // DHL FeDex
                'weight' => 1,
                'first'  => 15,
                'per'    => 5,
                'per_weight' => 0.5,
                'days'   => '0-0',
                'currency' => 'USD',
                'count_unit' => 'LBS'
        );
?>
