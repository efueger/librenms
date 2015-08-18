<?php

$storage_cache = array();

foreach (dbFetchRows('SELECT * FROM storage WHERE device_id = ?', array($device['device_id'])) as $storage) {
    echo 'Storage '.$storage['storage_descr'].': ';

    $storage_rrd = 'storage-'.$storage['storage_mib'].'-'.$storage['storage_descr'].'.rrd';

    rrdtool_create($storage_rrd, '--step 300 DS:used:GAUGE:600:0:U DS:free:GAUGE:600:0:U '.$config['rrd_rra']);

    $file = $config['install_dir'].'/includes/polling/storage/'.$storage['storage_mib'].'.inc.php';
    if (is_file($file)) {
        include $file;
    }
    else {
        // FIXME Generic poller goes here if we ever have a discovery module which uses it.
    }

    if ($debug) {
        print_r($storage);
    }

    if ($storage['size']) {
        $percent = round(($storage['used'] / $storage['size'] * 100));
    }
    else {
        $percent = 0;
    }

    echo $percent.'% ';

    $fields = array(
        'used'   => $storage['used'],
        'free'   => $storage['free'],
    );

    rrdtool_update($storage_rrd, $fields);

    if ($config['memcached']['enable'] === true) {
        $memcache->set('storage-'.$storage['storage_id'].'-used', $storage['used']);
        $memcache->set('storage-'.$storage['storage_id'].'-free', $storage['free']);
        $memcache->set('storage-'.$storage['storage_id'].'-size', $storage['size']);
        $memcache->set('storage-'.$storage['storage_id'].'-units', $storage['units']);
        $memcache->set('storage-'.$storage['storage_id'].'-perc', $percent);
    }
    else {
        $update = dbUpdate(array('storage_used' => $storage['used'], 'storage_free' => $storage['free'], 'storage_size' => $storage['size'], 'storage_units' => $storage['units'], 'storage_perc' => $percent), 'storage', '`storage_id` = ?', array($storage['storage_id']));
    }

    echo "\n";
}//end foreach

unset($storage);
