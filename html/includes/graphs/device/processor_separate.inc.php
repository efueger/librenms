<?php

$i = 0;

foreach ($procs as $proc) {
    $rrd_filename = 'processor-'.$proc['processor_type'].'-'.$proc['processor_index'].'.rrd';
    $descr = short_hrDeviceDescr($proc['processor_descr']);
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr']    = $descr;
    $rrd_list[$i]['ds']       = 'usage';
    $rrd_list[$i]['area']     = 1;
    $i++;
}

$unit_text = 'Load %';

$units       = '%';
$total_units = '%';
$colours     = 'mixed';

$scale_min = '0';
$scale_max = '100';

$nototal = 1;

require 'includes/graphs/generic_multi_line.inc.php';
