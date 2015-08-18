<?php

if ($port_stats[$port['ifIndex']] &&
    $port['ifType'] == 'ethernetCsmacd' &&
    isset($port_stats[$port['ifIndex']]['dot3StatsIndex'])) {
    // Check to make sure Port data is cached.
    $this_port = &$port_stats[$port[ifIndex]];

    $rrdfile     = 'port-'.$port['ifIndex'].'-dot3.rrd';

    $rrd_create = $config['rrd_rra'];

    foreach ($etherlike_oids as $oid) {
        $oid         = truncate(str_replace('dot3Stats', '', $oid), 19, '');
        $rrd_create .= " DS:$oid:COUNTER:600:U:100000000000";
    }

    rrdtool_create($rrdfile, $rrd_create);

    $fields = array();
    foreach ($etherlike_oids as $oid) {
        $data           = ($this_port[$oid] + 0);
        $fields[$oid] = $data;
    }

    rrdtool_update($rrdfile, $fields);

    echo 'EtherLike ';
}
