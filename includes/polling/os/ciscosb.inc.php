<?php
/*
* LibreNMS Cisco Small Business OS information module
*
* Copyright (c) 2015 Mike Rostermund <mike@kollegienet.dk>
* This program is free software: you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation, either version 3 of the License, or (at your
* option) any later version.  Please see LICENSE.txt at the top level of
* the source code distribution for details.
*/
$version = snmp_get($device, "CISCOSB-Physicaldescription-MIB::rlPhdUnitGenParamSoftwareVersion.1", "-Ovq");
$hardware = str_replace(' ', '', snmp_get($device, "CISCOSB-Physicaldescription-MIB::rlPhdUnitGenParamModelName.1", "-Ovq"));
$serial = snmp_get($device, "CISCOSB-Physicaldescription-MIB::rlPhdUnitGenParamSerialNum.1", "-Ovq");
$features = snmp_get($device, "CISCOSB-Physicaldescription-MIB::rlPhdUnitGenParamServiceTag.1", "-Ovq");
?>
