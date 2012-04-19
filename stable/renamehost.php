#!/usr/bin/env php
<?php

/**
 * Observium Network Management and Monitoring System
 * Copyright (C) 2006-2011, Observium Developers - http://www.observium.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See COPYING for more details.
 *
 * @package    observium
 * @subpackage cli
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2012 Adam Armstrong
 * @license    http://gnu.org/copyleft/gpl.html GNU GPL
 *
 */

chdir(dirname($argv[0]));

include("includes/defaults.inc.php");
include("config.php");
include("includes/functions.php");

# Remove a host and all related data from the system

if ($argv[1] && $argv[2])
{
  $host = strtolower($argv[1]);
  $id = getidbyname($host);
  if ($id)
  {
    $tohost = strtolower($argv[2]);
    $toid = getidbyname($tohost);
    if ($toid)
    {
      echo("NOT renamed. New hostname $tohost already exists.\n");
    } else {
      renamehost($id, $tohost, 'console');
      echo("Renamed $host\n");
    }
  } else {
    echo("Host doesn't exist!\n");
  }
}
else
{
  echo("Host Rename Tool\nUsage: ./renamehost.php <old hostname> <new hostname>\n");
}

?>
