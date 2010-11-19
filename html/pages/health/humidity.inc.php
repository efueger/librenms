<?php

if($_SESSION['userlevel'] >= '5') {
  $sql = "SELECT * FROM `sensors` AS S, `devices` AS D WHERE S.sensor_class='humidity' AND S.device_id = D.device_id ORDER BY D.hostname, S.sensor_index, S.sensor_descr";
} else {
  $sql = "SELECT * FROM `sensors` AS S, `devices` AS D, devices_perms as P WHERE S.sensor_class='humidity' AND S.device_id = D.device_id AND D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' ORDER BY D.hostname, S.sensor_index, S.sensor_descr";
}

$query = mysql_query($sql);

$graph_type = "sensor_humidity";

echo('<table cellspacing="0" cellpadding="6" width="100%">');

echo('<tr class=tablehead>
        <th width="280">Device</th>
        <th width="280">Sensor</th>
	<th></th>
	<th></th>
        <th width="100">Current</th>
        <th width="100">Alert</th>
        <th>Notes</th>
      </tr>');

$row = 1;

while($humidity = mysql_fetch_array($query))
{
  if(is_integer($row/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

  $weekly_humidity  = "graph.php?id=" . $humidity['sensor_id'] . "&amp;type=".$graph_type."&amp;from=$week&amp;to=$now&amp;width=500&amp;height=150";
  $humidity_popup = "<a onmouseover=\"return overlib('<img src=\'$weekly_humidity\'>', LEFT);\" onmouseout=\"return nd();\">
        " . $humidity['sensor_descr'] . "</a>";

  $humidity['sensor_current'] = round($humidity['sensor_current'],1);

  $humidity_perc = $humidity['sensor_current'] / $humidity['sensor_limit'] * 100;
  $humidity_colour = percent_colour($humidity_perc);

  if($humidity['sensor_current'] >= $humidity['sensor_limit']) { $alert = '<img src="images/16/flag_red.png" alt="alert" />'; } else { $alert = ""; }
   
  $humidity_day    = "graph.php?id=" . $humidity['sensor_id'] . "&amp;type=".$graph_type."&amp;from=$day&amp;to=$now&amp;width=300&amp;height=100";
  $humidity_week   = "graph.php?id=" . $humidity['sensor_id'] . "&amp;type=".$graph_type."&amp;from=$week&amp;to=$now&amp;width=300&amp;height=100";
  $humidity_month  = "graph.php?id=" . $humidity['sensor_id'] . "&amp;type=".$graph_type."&amp;from=$month&amp;to=$now&amp;width=300&amp;height=100";
  $humidity_year   = "graph.php?id=" . $humidity['sensor_id'] . "&amp;type=".$graph_type."&amp;from=$year&amp;to=$now&amp;width=300&amp;height=100";

  $humidity_minigraph = "<img src='graph.php?id=" . $humidity['sensor_id'] . "&amp;type=".$graph_type."&amp;from=$day&amp;to=$now&amp;width=100&amp;height=20'";
  $humidity_minigraph .= " onmouseover=\"return overlib('<div class=list-large>".$humidity['hostname']." - ".$humidity['sensor_descr'];
  $humidity_minigraph .= "</div><div style=\'width: 750px\'><img src=\'$humidity_day\'><img src=\'$humidity_week\'><img src=\'$humidity_month\'><img src=\'$humidity_year\'></div>', RIGHT".$config['overlib_defaults'].");\" onmouseout=\"return nd();\" >";

  echo("<tr bgcolor=$row_colour>
          <td class=list-bold>" . generate_device_link($humidity) . "</td>
          <td>$humidity_popup</td>
	  <td>$humidity_minigraph</td>
	  <td width=100>$alert</td>
          <td style='color: $humidity_colour; text-align: center; font-weight: bold;'>" . $humidity['sensor_current'] . " %</td>
          <td style='text-align: center'>" . $humidity['sensor_limit'] . " %</td>
          <td>" . (isset($humidity['sensor_notes']) ? $humidity['sensor_notes'] : '') . "</td>
        </tr>\n");

      if($_GET['optb'] == "graphs") { ## If graphs

  echo("<tr bgcolor='$row_colour'><td colspan=7>");

  $daily_graph   = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$day&to=$now&width=211&height=100";
  $daily_url       = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$day&to=$now&width=400&height=150";

  $weekly_graph  = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$week&to=$now&width=211&height=100";
  $weekly_url      = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$week&to=$now&width=400&height=150";

  $monthly_graph = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$month&to=$now&width=211&height=100";
  $monthly_url     = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$month&to=$now&width=400&height=150";

  $yearly_graph  = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$year&to=$now&width=211&height=100";
  $yearly_url  = "graph.php?id=" . $humidity['sensor_id'] . "&type=".$graph_type."&from=$year&to=$now&width=400&height=150";

  echo("<a onmouseover=\"return overlib('<img src=\'$daily_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$daily_graph' border=0></a> ");
  echo("<a onmouseover=\"return overlib('<img src=\'$weekly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$weekly_graph' border=0></a> ");
  echo("<a onmouseover=\"return overlib('<img src=\'$monthly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$monthly_graph' border=0></a> ");
  echo("<a onmouseover=\"return overlib('<img src=\'$yearly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$yearly_graph' border=0></a>");
  echo("</td></tr>");

    } # endif graphs



  $row++;

}

echo("</table>");


?>

