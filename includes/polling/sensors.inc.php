<?php

poll_sensor($device,'current','A');
poll_sensor($device,'frequency', 'Hz');
poll_sensor($device,'fanspeed', 'rpm');
poll_sensor($device,'humidity', '%');
poll_sensor($device,'voltage', 'V');

# FIXME also convert temperature, but there's some special code in there?
include('includes/polling/temperatures.inc.php');

?>