<?php
/*
* LibreNMS
*
* Copyright (c) 2015 Søren Friis Rosiak <sorenrosiak@gmail.com>
* This program is free software: you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation, either version 3 of the License, or (at your
* option) any later version.  Please see LICENSE.txt at the top level of
* the source code distribution for details.
*/

if (!$os) {
    $ibmtl_snmpget = snmp_get($device, 'SML-MIB:product-Name.0', '-Oqv', '');
    if (!empty($ibmtl_snmpget)) {
        $os = 'ibmtl';
    }
}