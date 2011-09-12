<?php

if ($_GET['opta']) { $_GET['opta'] = mres($_GET['opta']); }

if ($_GET['optb'] == "port" && is_numeric($_GET['opta']) && port_permitted($_GET['optc']))
{
  $check_device = get_device_id_by_interface_id($_GET['opta']);
  $permit_ports = 1;
}

if (device_permitted($_GET['opta']) || $check_device == $_GET['opta'])
{
  $selected['iface'] = "selected";

  $section = str_replace(".", "", mres($_GET['optb']));

  if (!$section)
  {
    $section = "overview";
  }

  $select[$section] = "selected";

  $device  = device_by_id_cache($_GET['opta']);
  $attribs = get_dev_attribs($device['device_id']);

  if ($config['os'][$device['os']]['group']) { $device['os_group'] = $config['os'][$device['os']]['group']; }

  echo('<table style="margin: 0px 7px 7px 7px;" cellpadding="15" cellspacing="0" class="devicetable" width="99%">');
  #include("includes/hostbox.inc.php");
  include("includes/device-header.inc.php");
  echo('</table>');

  echo('<div class="mainpane">');
  echo('  <ul id="maintab" class="shadetabs">');

  if (device_permitted($device['device_id']))
  {
    if ($config['show_overview_tab'])
    {
      echo('
  <li class="' . $select['overview'] . '">
    <a href="device/' . $device['device_id'] . '/overview/">
      <img src="images/16/server_lightning.png" align="absmiddle" border="0"> Overview
    </a>
  </li>');
    }

    echo('<li class="' . $select['graphs'] . '">
    <a href="device/' . $device['device_id'] . '/graphs/">
      <img src="images/16/server_chart.png" align="absmiddle" border="0"> Graphs
    </a>
  </li>');

    $health =  dbFetchCell("SELECT COUNT(*) FROM storage WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(sensor_id) FROM sensors WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(*) FROM cempMemPool WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(*) FROM cpmCPU WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(*) FROM processors WHERE device_id = '" . $device['device_id'] . "'");

    if ($health)
    {
      echo('<li class="' . $select['health'] . '">
      <a href="device/' . $device['device_id'] . '/health/">
        <img src="images/icons/sensors.png" align="absmiddle" border="0" /> Health
      </a>
    </li>');
    }

    if (@dbFetchCell("SELECT COUNT(app_id) FROM applications WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['apps'] . '">
    <a href="device/' . $device['device_id'] . '/apps/">
      <img src="images/icons/apps.png" align="absmiddle" border="0" /> Apps
    </a>
  </li>');
    }

    if (is_dir($config['collectd_dir'] . "/" . $device['hostname'] ."/"))
    {
      echo('<li class="' . $select['collectd'] . '">
    <a href="device/' . $device['device_id'] . '/collectd/">
      <img src="images/16/chart_line.png" align="absmiddle" border="0" /> CollectD
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(interface_id) FROM ports WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['ports'] . $select['port'] . '">
    <a href="device/' . $device['device_id'] . '/ports/' .$config['ports_page_default']. '">
      <img src="images/16/connect.png" align="absmiddle" border="0" /> Ports
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(vlan_id) FROM vlans WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['vlans'] . '">
    <a href="device/' . $device['device_id'] . '/vlans/">
      <img src="images/16/vlans.png" align="absmiddle" border="0" /> VLANs
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(id) FROM vminfo WHERE device_id = '" . $device["device_id"] . "'") > '0')
    {
      echo('<li class="' . $select['vm'] . '">
    <a href="device/' . $device['device_id'] . '/vm/">
      <img src="images/16/server_cog.png" align="absmiddle" border="0" /> Virtual Machines
    </a>
  </li>');
    }

    ### $routing_tabs is used in device/routing/ to build the tabs menu. we built it here to save some queries

    $device_routing_count['ipsec_tunnels'] = dbFetchCell("SELECT COUNT(*) FROM `ipsec_tunnels` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['ipsec_tunnels']) { $routing_tabs[] = 'ipsec_tunnels'; }

    $device_routing_count['bgp'] = dbFetchCell("SELECT COUNT(*) FROM `bgpPeers` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['bgp']) { $routing_tabs[] = 'bgp'; }
  
    $device_routing_count['ospf'] = dbFetchCell("SELECT COUNT(*) FROM `ospf_ports` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['ospf']) { $routing_tabs[] = 'ospf'; }

    $device_routing_count['cef'] = dbFetchCell("SELECT COUNT(*) FROM `cef_switching` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['cef']) { $routing_tabs[] = 'cef'; }

    $device_routing_count['vrf'] = @dbFetchCell("SELECT COUNT(*) FROM `vrfs` WHERE `device_id` = ?", array($device['device_id']));
    if($device_routing_count['vrf']) { $routing_tabs[] = 'vrf'; }

    if (is_array($routing_tabs)) 
    {
      echo('<li class="' . $select['routing'] . '">
    <a href="device/' . $device['device_id'] . '/routing/">
      <img src="images/16/arrow_branch.png" align="absmiddle" border="0" /> Routing
    </a>
  </li>');
    }

    if ($_SESSION['userlevel'] >= "5" && dbFetchCell("SELECT COUNT(*) FROM links AS L, ports AS I WHERE I.device_id = '".$device['device_id']."' AND I.interface_id = L.local_interface_id"))
    {
      $discovery_links = TRUE;
      echo('<li class="' . $select['map'] . '">
    <a href="device/' . $device['device_id'] . '/map/">
      <img src="images/16/chart_organisation.png" align="absmiddle" border="0" /> Map
    </a>
  </li>');
    }

    if ($config['enable_inventory'] && @dbFetchCell("SELECT * FROM `entPhysical` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['entphysical'] . '">
    <a href="device/' . $device['device_id'] . '/entphysical/">
      <img src="images/16/bricks.png" align="absmiddle" border="0" /> Inventory
    </a>
  </li>');
    }
    elseif (device_permitted($device['device_id']) && $config['enable_inventory'] && @dbFetchCell("SELECT * FROM `hrDevice` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['hrdevice'] . '">
    <a href="device/' . $device['device_id'] . '/hrdevice/">
      <img src="images/16/bricks.png" align="absmiddle" border="0" /> Inventory
    </a>
  </li>');
    }

    if (dbFetchCell("SELECT COUNT(service_id) FROM services WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['srv'] . '">
    <a href="device/' . $device['device_id'] . '/services/">
      <img src="images/icons/services.png" align="absmiddle" border="0" /> Services
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(toner_id) FROM toner WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['toner'] . '">
    <a href="device/' . $device['device_id'] . '/toner/">
      <img src="images/icons/toner.png" align="absmiddle" border="0" /> Toner
    </a>
  </li>');
    }

    if (device_permitted($device['device_id']))
    {
      echo('<li class="' . $select['events'] . '">
      <a href="device/' . $device['device_id'] . '/events/">
        <img src="images/16/report_magnify.png" align="absmiddle" border="0" /> Events
      </a>
    </li>');
    }

    if ($config['enable_syslog'])
    {
      echo('<li class="' . $select['syslog'] . '">
    <a href="device/' . $device['device_id'] . '/syslog/">
      <img src="images/16/printer.png" align="absmiddle" border="0" /> Syslog
    </a>
  </li>');
    }

    if ($_SESSION['userlevel'] >= "7")
    {
      if (!is_array($config['rancid_configs'])) { $config['rancid_configs'] = array($config['rancid_configs']); }
      foreach ($config['rancid_configs'] as $configs)
      {
        if ($configs[strlen($configs)-1] != '/') { $configs .= '/'; }
        if (is_file($configs . $device['hostname'])) { $device_config_file = $configs . $device['hostname']; }
      }
    }

    if ($device_config_file)
    {
      echo('<li class="' . $select['showconfig'] . '">
    <a href="device/' . $device['device_id'] . '/showconfig/">
      <img src="images/16/page_white_text.png" align="absmiddle" border="0" /> Config
    </a>
  </li>');
    }

    if ($config['nfsen_enable'])
    {   
      if (!is_array($config['nfsen_rrds'])) { $config['nfsen_rrds'] = array($config['nfsen_rrds']); }
      foreach ($config['nfsen_rrds'] as $nfsenrrds)
      {
        if ($configs[strlen($nfsenrrds)-1] != '/') { $nfsenrrds .= '/'; }
        $nfsensuffix = "";
        if ($config['nfsen_suffix']) { $nfsensuffix = $config['nfsen_suffix']; }
        $basefilename_underscored = preg_replace('/\./', $config['nfsen_split_char'], $device['hostname']);
        $nfsen_filename = (strstr($basefilename_underscored, $nfsensuffix, true));
        if (is_file($nfsenrrds . $nfsen_filename . ".rrd")) { $nfsen_rrd_file = $nfsenrrds . $basefilename_underscored . ".rrd"; }
      }
    }

    if ($nfsen_rrd_file)
    {
      echo('<li class="' . $select['nfsen'] . '">
    <a href="device/' . $device['device_id'] . '/nfsen/">
      <img src="images/16/rainbow.png" align="absmiddle" border="0" /> Netflow
    </a>
  </li>');
    }


    if ($_SESSION['userlevel'] >= "7")
    {
      echo('<li class="' . $select['edit'] . '" style="text-align: right;">
    <a href="device/' . $device['device_id'] . '/edit/">
      <img src="images/16/server_edit.png" align="absmiddle" border="0" /> Settings
    </a>
  </li>');
    }
    echo("</ul>");
  }

  if(device_permitted($device['device_id']) || $check_device == $_GET['opta']) {
    echo('<div class="contentstyle">');

    include("pages/device/".mres(basename($section)).".inc.php");

    echo("</div>");
  } else {
    include("includes/error-no-perm.inc.php");
  }
}

?>
