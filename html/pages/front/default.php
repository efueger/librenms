<?php

require_once("includes/front/top_graphs.inc.php");

function generate_front_box ($frontbox_class, $content)
{
echo("<div class=\"front-box $frontbox_class\">
      $content
      </div>");
}

echo('<div class=front-page>');

echo('<div class="status-boxes left-2-col-fluid">');

$count_boxes = 0;

// Device down boxes
if ($_SESSION['userlevel'] == '10')
{
$sql = mysql_query("SELECT * FROM `devices` WHERE `status` = '0' AND `ignore` = '0'");
} else {
$sql = mysql_query("SELECT * FROM `devices` AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND D.status = '0' AND D.ignore = '0'");
}
while ($device = mysql_fetch_assoc($sql)) {

  generate_front_box("device-down", generate_device_link($device, shorthost($device['hostname']))."<br />
    <span class=list-device-down>Device Down</span> <br />
    <span class=body-date-1>".truncate($device['location'], 20)."</span>");
  ++$count_boxes;
}

if ($_SESSION['userlevel'] == '10')
{
$sql = mysql_query("SELECT * FROM `ports` AS I, `devices` AS D WHERE I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up' AND D.ignore = '0' AND I.ignore = '0'");
} else {
$sql = mysql_query("SELECT * FROM `ports` AS I, `devices` AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND  I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up' AND D.ignore = '0' AND I.ignore = '0'");
}

// These things need to become more generic, and more manageable across different frontpages... rewrite inc :>

// Port down boxes
if ($config['warn']['ifdown'])
{
  while ($interface = mysql_fetch_assoc($sql))
  {
    if (!$interface['deleted'])
    {
      $interface = ifNameDescr($interface);
      generate_front_box("port-down", generate_device_link($interface, shorthost($interface['hostname']))."<br />
        <span class=\"interface-updown\">Port Down</span><br />
<!--      <img src='graph.php?type=bits&amp;if=".$interface['port_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=100&amp;height=32' /> -->
        ".generate_port_link($interface, truncate(makeshortif($interface['label']),13,''))." <br />
        " . ($interface['ifAlias'] ? '<span class="body-date-1">'.truncate($interface['ifAlias'], 20, '').'</span>' : ''));
      ++$count_boxes;
    }
  }
}

/* FIXME service permissions? seem nonexisting now.. */
// Service down boxes
$sql = mysql_query("SELECT * FROM `services` AS S, `devices` AS D WHERE S.device_id = D.device_id AND service_status = 'down' AND D.ignore = '0' AND S.service_ignore = '0'");
while ($service = mysql_fetch_assoc($sql))
{
  generate_front_box("service-down", generate_device_link($service, shorthost($service['hostname']))."<br />
    <span class=service-down>Service Down</span>
    ".$service['service_type']."<br />
    <span class=body-date-1>".truncate($interface['ifAlias'], 20)."</span>");
  ++$count_boxes;
}

// BGP neighbour down boxes
if (isset($config['enable_bgp']) && $config['enable_bgp'])
{
  if ($_SESSION['userlevel'] == '10')
  {
    $sql = mysql_query("SELECT * FROM `devices` AS D, bgpPeers AS B WHERE bgpPeerAdminStatus != 'start' AND bgpPeerState != 'established' AND bgpPeerState != '' AND B.device_id = D.device_id AND D.ignore = 0");
  } else {
    $sql = mysql_query("SELECT * FROM `devices` AS D, bgpPeers AS B, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND  bgpPeerAdminStatus != 'start' AND bgpPeerState != 'established' AND bgpPeerState != '' AND B.device_id = D.device_id AND D.ignore = 0");
  }
  while ($peer = mysql_fetch_assoc($sql))
  {
  generate_front_box("bgp-down", generate_device_link($peer, shorthost($peer['hostname']))."<br />
    <span class=bgp-down>BGP Down</span>
    <span class='" . (strstr($peer['bgpPeerIdentifier'],':') ? 'front-page-bgp-small' : 'front-page-bgp-normal') . "'>".$peer['bgpPeerIdentifier']."</span><br />
    <span class=body-date-1>AS".truncate($peer['bgpPeerRemoteAs']." ".$peer['astext'], 14, "")."</span>");
    ++$count_boxes;
  }
}

// Device rebooted boxes
if (filter_var($config['uptime_warning'], FILTER_VALIDATE_FLOAT) !== FALSE && $config['uptime_warning'] > 0)
{
  if ($_SESSION['userlevel'] == '10')
  {
  $sql = mysql_query("SELECT * FROM `devices` AS D WHERE D.status = '1' AND D.uptime > 0 AND D.uptime < '" . $config['uptime_warning'] . "' AND D.ignore = 0");
  } else {
  $sql = mysql_query("SELECT * FROM `devices` AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND D.status = '1' AND D.uptime > 0 AND D.uptime < '" .
  $config['uptime_warning'] . "' AND D.ignore = 0");
  }

  while ($device = mysql_fetch_assoc($sql))
  {
    generate_front_box("device-rebooted", generate_device_link($device, shorthost($device['hostname']))."<br />
      <span class=device-rebooted>Device Rebooted</span><br />
      <span class=body-date-1>".formatUptime($device['uptime'], 'short')."</span>");
    ++$count_boxes;
  }
}
if ($count_boxes == 0) {
  echo("<h5>Nothing here yet</h5><p class=welcome>This is where status notifications about devices and services would normally go. You might have none
  because you run such a great network, or perhaps you've just started using ".$config['project_name'].". If you're new to ".$config['project_name'].", you might
  want to start by adding one or more devices in the Devices menu.</p>");
}
echo('</div>');

if ($config['enable_syslog'])
{
  echo("<div class=front-syslog>
    <h3>Recent Syslog Messages</h3>
  ");

  $sql = "SELECT *, DATE_FORMAT(timestamp, '%D %b %T') AS date from syslog ORDER BY timestamp DESC LIMIT 20";
  $query = mysql_query($sql);
  echo("<table cellspacing=0 cellpadding=2 width=100%>");
  while ($entry = mysql_fetch_assoc($query))
  {
    $entry = array_merge($entry, device_by_id_cache($entry['device_id']));

    include("includes/print-syslog.inc.php");
  }
  echo("</table>");
  echo("</div>");

} else {
  echo("<div class=front-eventlog>
    <h3>Recent Eventlog Entries</h3>
  ");

  if ($_SESSION['userlevel'] == '10')
  {
    $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %T') as humandate  FROM `eventlog` ORDER BY `datetime` DESC LIMIT 0,15";
  } else {
    $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %T') as humandate  FROM `eventlog` AS E, devices_perms AS P WHERE E.host =
    P.device_id AND P.user_id = " . $_SESSION['user_id'] . " ORDER BY `datetime` DESC LIMIT 0,15";
  }

  $data = mysql_query($query);

  echo('<table cellspacing="0" cellpadding="1" width="100%">');

  while ($entry = mysql_fetch_assoc($data)) {
    include("includes/print-event.inc.php");
  }

  echo("</table>");
  echo("</div>");
}

echo("</div>");

?>
