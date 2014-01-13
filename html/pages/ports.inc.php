<?php

$pagetitle[] = "Ports";

// Set Defaults here

if(!isset($vars['format'])) { $vars['format'] = "list_basic"; }

print_optionbar_start();

echo('<span style="font-weight: bold;">Lists</span> &#187; ');

$menu_options = array('basic'      => 'Basic',
                      'detail'     => 'Detail');

$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['format'] == "list_".$option)
  {
    echo("<span class='pagemenu-selected'>");
  }
  echo('<a href="' . generate_url($vars, array('format' => "list_".$option)) . '">' . $text . '</a>');
  if ($vars['format'] == "list_".$option)
  {
    echo("</span>");
  }
  $sep = " | ";
}
?>

 |

<span style="font-weight: bold;">Graphs</span> &#187;

<?php

$menu_options = array('bits' => 'Bits',
                      'upkts' => 'Unicast Packets',
                      'nupkts' => 'Non-Unicast Packets',
                      'errors' => 'Errors');

$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['format'] == 'graph_'.$option)
  {
    echo('<span class="pagemenu-selected">');
  }
  echo('<a href="' . generate_url($vars, array('format' => 'graph_'.$option)) . '">' . $text . '</a>');
  if ($vars['format'] == 'graph_'.$option)
  {
    echo("</span>");
  }
  $sep = " | ";
}

echo('<div style="float: right;">');
?>

  <a href="<?php echo(generate_url($vars)); ?>" title="Update the browser URL to reflect the search criteria." >Update URL</a> |

<?php
  if ($vars['searchbar'] == "hide")
  {
    echo('<a href="'. generate_url($vars, array('searchbar' => '')).'">Search</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('searchbar' => 'hide')).'">Search</a>');
  }

  echo("  | ");

  if ($vars['bare'] == "yes")
  {
    echo('<a href="'. generate_url($vars, array('bare' => '')).'">Header</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('bare' => 'yes')).'">Header</a>');
  }

echo('</div>');

print_optionbar_end();
print_optionbar_start();

if($vars['searchbar'] != "hide")
{

?>
  <form method='post' action='' class='form-inline' role='form'>
    <div class="form-group">
      <select name='device_id' id='device_id' class='form-control input-sm'>
        <option value=''>All Devices</option>
<?php

foreach (dbFetchRows("SELECT `device_id`,`hostname` FROM `devices` GROUP BY `hostname` ORDER BY `hostname`") as $data)
{
  echo('        <option value="'.$data['device_id'].'"');
  if ($data['device_id'] == $vars['device_id']) { echo("selected"); }
  echo(">".$data['hostname']."</option>");
}
?>
      </select>
      <br />
      <input type="hostname" name="hostname" id="hostname" title="Hostname" class="form-control input-sm" <?php if (strlen($vars['hostname'])) {echo('value="'.$vars['hostname'].'"');} ?> />
    </div>
    <div class="form-group">
      <select name="state" id="state" class="form-control input-sm">
        <option value="">All States</option>
        <option value="up" <?php if ($vars['state'] == "up") { echo("selected"); } ?>>Up</option>
        <option value="down"<?php if ($vars['state'] == "down") { echo("selected"); } ?>>Down</option>
        <option value="admindown" <?php if ($vars['state'] == "admindown") { echo("selected"); } ?>>Shutdown</option>
      </select>
      <br />

      <select name="ifSpeed" id="ifSpeed" class="form-control input-sm">
        <option value="">All Speeds</option>
<?php
foreach (dbFetchRows("SELECT `ifSpeed` FROM `ports` GROUP BY `ifSpeed` ORDER BY `ifSpeed`") as $data)
{
  if ($data['ifSpeed'])
  {
    echo("<option value='".$data['ifSpeed']."'");
    if ($data['ifSpeed'] == $vars['ifSpeed']) { echo("selected"); }
    echo(">".humanspeed($data['ifSpeed'])."</option>");
  }
}
?>
       </select>
    </div>
    <div class="form-group">
      <select name="ifType" id="ifType" class="form-control input-sm">
        <option value="">All Media</option>
<?php
foreach (dbFetchRows("SELECT `ifType` FROM `ports` GROUP BY `ifType` ORDER BY `ifType`") as $data)
{
  if ($data['ifType'])
  {
    echo('        <option value="'.$data['ifType'].'"');
    if ($data['ifType'] == $vars['ifType']) { echo("selected"); }
    echo(">".$data['ifType']."</option>");
  }
}
?>
       </select>
<br />
      <select name="port_descr_type" id="port_descr_type" class="form-control input-sm">
        <option value="">All Port Types</option>
<?php
$ports = dbFetchRows("SELECT `port_descr_type` FROM `ports` GROUP BY `port_descr_type` ORDER BY `port_descr_type`");
$total = count($ports);
echo("Total: $total");
foreach ($ports as $data)
{
  if ($data['port_descr_type'])
  {
    echo('        <option value="'.$data['port_descr_type'].'"');
    if ($data['port_descr_type'] == $vars['port_descr_type']) { echo("selected"); }
    echo(">".ucfirst($data['port_descr_type'])."</option>");
  }
}
?>
      </select>
    </div>
    <div class="form-group">
      <input title="Port Description" type="text" name="ifAlias" id="ifAlias" class="form-control input-sm" <?php if (strlen($vars['ifAlias'])) {echo('value="'.$vars['ifAlias'].'"');} ?> />
        <select name="location" id="location" class="form-control input-sm">
          <option value="">All Locations</option>
          <?php
           // fix me function?

           foreach (getlocations() as $location) // FIXME function name sucks maybe get_locations ?
           {
             if ($location)
             {
               echo('<option value="'.$location.'"');
               if ($location == $vars['location']) { echo(" selected"); }
               echo(">".$location."</option>");
             }
           }
         ?>
        </select>
      </div>
      <div class="form-group">
        <label for="ignore">Ignored</label>
        <input type=checkbox id="ignore" name="ignore" value="1" class="" <?php if ($vars['ignore']) { echo("checked"); } ?> ></input>
        <br />
        <label for="disable">Disabled</label>
        <input type=checkbox id="disable" name="disable" value=1 class="" <?php if ($vars['disable']) { echo("checked"); } ?> ></input>
        </label>
        <br />
        <label for="deleted">Deleted</label>
        <input type=checkbox id="deleted" name="deleted" value=1 class="" <?php if ($vars['deleted']) { echo("checked"); } ?> ></input>
        </label>
      </div>
      <div class="form-group">
        <select name="sort" id="sort" class="form-control input-sm">
<?php
$sorts = array('device' => 'Device',
              'port' => 'Port',
              'speed' => 'Speed',
              'traffic' => 'Traffic',
              'traffic_in' => 'Traffic In',
              'traffic_out' => 'Traffic Out',
              'packets' => 'Packets',
              'packets_in' => 'Packets In',
              'packets_out' => 'Packets Out',
              'errors' => 'Errors',
              'media' => 'Media',
              'descr' => 'Description');

foreach ($sorts as $sort => $sort_text)
{
  echo('<option value="'.$sort.'" ');
  if ($vars['sort'] == $sort)  { echo("selected"); }
  echo('>'.$sort_text.'</option>');
}
?>

        </select>
      </div>
      <button type="submit" class="btn btn-default input-sm">Search</button>
      <a href="<?php echo(generate_url(array('page' => 'ports', 'section' => $vars['section'], 'bare' => $vars['bare']))); ?>" title="Reset critera to default." >Reset</a>
      </td>
    </form>
  </tr>
</table>

<?php }

print_optionbar_end();

$param = array();

if(!isset($vars['ignore']))   { $vars['ignore'] = "0"; }
if(!isset($vars['disabled'])) { $vars['disabled'] = "0"; }
if(!isset($vars['deleted']))  { $vars['deleted'] = "0"; }

foreach ($vars as $var => $value)
{
  if ($value != "")
  {
    switch ($var)
    {
      case 'hostname':
      case 'location':
        $where .= " AND D.$var LIKE ?";
        $param[] = "%".$value."%";
      case 'device_id':
      case 'deleted':
      case 'ignore':
        if ($value == 1)
        {
          $where .= " AND (I.ignore = 1 OR D.ignore = 1) AND I.deleted = 0";
        }
        break;
      case 'disable':
      case 'ifSpeed':
        if (is_numeric($value))
        {
          $where .= " AND I.$var = ?";
          $param[] = $value;
        }
        break;
      case 'ifType':
        $where .= " AND I.$var = ?";
        $param[] = $value;
        break;
      case 'ifAlias':
      case 'port_descr_type':
        $where .= " AND I.$var LIKE ?";
        $param[] = "%".$value."%";
        break;
      case 'errors':
        if ($value == 1)
        {
          $where .= " AND (I.`ifInErrors_delta` > '0' OR I.`ifOutErrors_delta` > '0')";
        }
        break;
      case 'state':
        if ($value == "down")
        {
          $where .= "AND I.ifAdminStatus = ? AND I.ifOperStatus = ?";
          $param[] = "up";
          $param[] = "down";
        } elseif($value == "up") {
          $where .= "AND I.ifAdminStatus = ? AND I.ifOperStatus = ?  AND I.ignore = '0' AND D.ignore='0' AND I.deleted='0'";
          $param[] = "up";
          $param[] = "up";
        } elseif($value == "admindown") {
          $where .= "AND I.ifAdminStatus = ? AND D.ignore = 0";
          $param[] = "down";
        }
      break;
    }
  }
}

$query = "SELECT * FROM `ports` AS I, `devices` AS D WHERE I.device_id = D.device_id ".$where." ".$query_sort;

$row = 1;

list($format, $subformat) = explode("_", $vars['format']);

$ports = dbFetchRows($query, $param);

// FIXME - only populate what we need to search at this point, because we shouldn't show *all* ports, as it's silly.

foreach ($ports as $port)
{
  if ($config['memcached']['enable'])
  {
    if ($config['memcached']['enable'])
    {
      $state = $memcache->get('port-'.$port['port_id'].'-state');
      if(is_array($state)) { $ports[$port['port_id']] = array_merge($port, $state); }
      unset($state);
    }
  }
}

switch ($vars['sort'])
{
  case 'traffic':
    $ports = array_sort($ports, 'ifOctets_rate', SORT_DESC);
    break;
  case 'traffic_in':
    $ports = array_sort($ports, 'ifInOctets_rate', SORT_DESC);
    break;
  case 'traffic_out':
    $ports = array_sort($ports, 'ifOutOctets_rate', SORT_DESC);
    break;
  case 'packets':
    $ports = array_sort($ports, 'ifUcastPkts_rate', SORT_DESC);
    break;
  case 'packets_in':
    $ports = array_sort($ports, 'ifInUcastOctets_rate', SORT_DESC);
    break;
  case 'packets_out':
    $ports = array_sort($ports, 'ifOutUcastOctets_rate', SORT_DESC);
    break;
  case 'errors':
    $ports = array_sort($ports, 'ifErrors_rate', SORT_DESC);
    break;
  case 'speed':
    $ports = array_sort($ports, 'ifSpeed', SORT_DESC);
    break;
  case 'port':
    $ports = array_sort($ports, 'ifDescr', SORT_ASC);
    break;
  case 'media':
    $ports = array_sort($ports, 'ifType', SORT_ASC);
    break;
  case 'descr':
    $ports = array_sort($ports, 'ifAlias', SORT_ASC);
    break;
  case 'device':
  default:
    $ports = array_sort($ports, 'hostname', SORT_ASC);
}

if(file_exists('pages/ports/'.$format.'.inc.php'))
{
 include('pages/ports/'.$format.'.inc.php');
}

?>
