<?php

if (!isset($vars['view']) ) { $vars['view'] = "graphs"; }

$port = dbFetchRow("SELECT * FROM `ports` WHERE `interface_id` = ?", array($vars['port']));

$port_details = 1;

$hostname = $device['hostname'];
$hostid   = $device['interface_id'];
$ifname   = $port['ifDescr'];
$ifIndex   = $port['ifIndex'];
$speed = humanspeed($port['ifSpeed']);

$ifalias = $port['name'];

if ($port['ifPhysAddress']) { $mac = "$port[ifPhysAddress]"; }

$color = "black";
if ($port['ifAdminStatus'] == "down") { $status = "<span class='grey'>Disabled</span>"; }
if ($port['ifAdminStatus'] == "up" && $port['ifOperStatus'] == "down") { $status = "<span class='red'>Enabled / Disconnected</span>"; }
if ($port['ifAdminStatus'] == "up" && $port['ifOperStatus'] == "up") { $status = "<span class='green'>Enabled / Connected</span>"; }

$i = 1;
$inf = fixifName($ifname);

$bg="#ffffff";

$show_all = 1;

echo("<div class=ifcell style='margin: 0px;'><table width=100% cellpadding=10 cellspacing=0>");

include("includes/print-interface.inc.php");

echo("</table></div>");

$pos = strpos(strtolower($ifname), "vlan");
if ($pos !== false )
{
  $broke = yes;
}

$pos = strpos(strtolower($ifname), "loopback");

if ($pos !== false )
{
  $broke = yes;
}

echo("<div style='clear: both;'>");

print_optionbar_start();

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'port',
                    'port'    => $port['interface_id']);

$menu_options['graphs']   = 'Graphs';
$menu_options['realtime'] = 'Real time';   ### FIXME CONDITIONAL
$menu_options['arp']      = 'ARP Table';
$menu_options['events']      = 'Eventlog';

if (dbFetchCell("SELECT COUNT(*) FROM `ports_adsl` WHERE `interface_id` = '".$port['interface_id']."'") )
{  $menu_options['adsl'] = 'ADSL'; }

if (dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE `pagpGroupIfIndex` = '".$port['ifIndex']."' and `device_id` = '".$device['device_id']."'") )
{  $menu_options['pagp'] = 'PAgP'; }

$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['view'] == $option) { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link($text,$link_array,array('view'=>$option)));
  if ($vars['view'] == $option) { echo("</span>"); }
  $sep = " | ";
}
unset($sep);

if (dbFetchCell("SELECT count(*) FROM mac_accounting WHERE interface_id = '".$port['interface_id']."'") > "0" )
{

  echo(generate_link($descr,$link_array,array('view'=>'macaccounting','graph'=>$type)));

  echo(" | Mac Accounting : ");
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "bits" && $vars['subview'] == "graphs") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Bits",$link_array,array('view' => 'macaccounting', 'subview' => 'graphs', 'graph'=>'bits')));
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "bits" && $vars['subview'] == "graphs") { echo("</span>"); }

  echo("(");
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "bits" && $vars['subview'] == "minigraphs") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Mini",$link_array,array('view' => 'macaccounting', 'subview' => 'minigraphs', 'graph'=>'bits')));
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "bits" && $vars['subview'] == "minigraphs") { echo("</span>"); }
  echo('|');

  if ($vars['view'] == "macaccounting" && $vars['graph'] == "bits" && $vars['subview'] == "top10") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Top10",$link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>'bits')));
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "bits" && $vars['subview'] == "top10") { echo("</span>"); }
  echo(") | ");

  if ($vars['view'] == "macaccounting" && $vars['graph'] == "pkts" && $vars['subview'] == "graphs") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Packets",$link_array,array('view' => 'macaccounting', 'subview' => 'graphs', 'graph'=>'pkts')));
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "pkts" && $vars['subview'] == "graphs") { echo("</span>"); }
  echo("(");
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "pkts" && $vars['subview'] == "minigraphs") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Mini",$link_array,array('view' => 'macaccounting', 'subview' => 'minigraphs', 'graph'=>'pkts')));
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "pkts" && $vars['subview'] == "minigraphs") { echo("</span>"); }
  echo('|');
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "pkts" && $vars['subview'] == "top10") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Top10",$link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>'pkts')));
  if ($vars['view'] == "macaccounting" && $vars['graph'] == "pkts" && $vars['subview'] == "top10") { echo("</span>"); }
  echo(")");
}

if (dbFetchCell("SELECT COUNT(*) FROM juniAtmVp WHERE interface_id = '".$port['interface_id']."'") > "0" )
{

  ### FIXME ATM VPs
  ### FIXME URLs BROKEN

  echo(" | ATM VPs : ");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "bits") { echo("<span class='pagemenu-selected'>"); }
  echo("<a href='/device/device=" . $device['device_id'] . "/tab=port/port=".$port['interface_id']."/junose-atm-vp/bits/'>Bits</a>");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "bits") { echo("</span>"); }
  echo(" | ");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "packets") { echo("<span class='pagemenu-selected'>"); }
  echo("<a href='device/device=" . $device['device_id'] . "/tab=port/port=".$port['interface_id']."/junose-atm-vp/packets/'>Packets</a>");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "bits") { echo("</span>"); }
  echo(" | ");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "cells") { echo("<span class='pagemenu-selected'>"); }
  echo("<a href='device/device=" . $device['device_id'] . "/tab=port/port=".$port['interface_id']."/junose-atm-vp/cells/'>Cells</a>");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "bits") { echo("</span>"); }
  echo(" | ");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "errors") { echo("<span class='pagemenu-selected'>"); }
  echo("<a href='device/device=" . $device['device_id'] . "/tab=port/port=".$port['interface_id']."/junose-atm-vp/errors/'>Errors</a>");
  if ($vars['view'] == "junose-atm-vp" && $vars['graph'] == "bits") { echo("</span>"); }
}

print_optionbar_end();

echo("<div style='margin: 5px;'>");
include("pages/device/port/".mres($vars['view']).".inc.php");
echo("</div>");

?>
