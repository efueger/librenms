<?php
/*
 * LibreNMS front page graphs
 *
 * Copyright (c) 2013 Gear Consulting Pty Ltd <http://libertysys.com.au/>
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */
?>
<?php

echo('
<div class="cycle-slideshow"
    data-cycle-fx="scrollVert"
    data-cycle-timeout="10000"
    data-cycle-slides="> div">
');

foreach (get_matching_files($config['html_dir']."/includes/front/", "/^top_.*\.php$/") as $file) {
  echo("<div class=box>\n");
  include_once($file);
  echo("</div>\n");
}

echo("</div>\n");

?>

