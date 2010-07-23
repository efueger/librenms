<?php

echo("Doing Juniper Netscreen (ScreenOS)");

$version = preg_replace("/(.+)\ version\ (.+)\ \(SN:\ (.+)\,\ (.+)\)/", "Juniper Netscreen \\1||\\2||\\3||\\4", $sysDescr);
#echo("$version\n");
list($hardware,$version,$serial,$features) = explode("||", $version);

$sessrrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/screenos-sessions.rrd";

$sess_cmd  = $config['snmpget'] . " -M ".$config['mibdir'] . " -O qv -" . $device['snmpver'] . " -c " . $device['community'] . " " . $device['hostname'];
$sess_cmd .= " .1.3.6.1.4.1.3224.16.3.2.0 .1.3.6.1.4.1.3224.16.3.3.0 .1.3.6.1.4.1.3224.16.3.4.0";
$sess_data = shell_exec($sess_cmd);
list ($sessalloc, $sessmax, $sessfailed) = explode("\n", $sess_data);

if (!is_file($sessrrd)) {
   `rrdtool create $sessrrd \
    --step 300 \
     DS:allocate:GAUGE:600:0:3000000 \
     DS:max:GAUGE:600:0:3000000 \
     DS:failed:GAUGE:600:0:1000 \
     RRA:AVERAGE:0.5:1:800 \
     RRA:AVERAGE:0.5:6:800 \
     RRA:AVERAGE:0.5:24:800 \
     RRA:AVERAGE:0.5:288:800 \
     RRA:MAX:0.5:1:800 \
     RRA:MAX:0.5:6:800 \
     RRA:MAX:0.5:24:800 \
     RRA:MAX:0.5:288:800`;
}

shell_exec($config['rrdtool'] . " update $sessrrd N:$sessalloc:$sessmax:$sessfailed");

?>
