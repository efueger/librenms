<?php

echo("Doing Thompson Speedtouch ");

$serial = "";
#list(,$hardware,) = explode(" ", $hardware);
$hardware = $sysDescr;

$features = "";

### Filthy hack to get software version. may not work on anything but 585v7 :)

$loop = shell_exec($config['snmpget'] . " -M ".$config['mibdir'] . ' -Ovq -v1 -c '.$device['community'].' '.$device['hostname'].' ifDescr.101');
if($loop) { 
  preg_match('@([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)@i',
    $loop, $matches);
    $version = $matches[1];
 }

?>
