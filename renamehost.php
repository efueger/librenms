#!/usr/bin/env php
<?php

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
