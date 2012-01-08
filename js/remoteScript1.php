<?php

$src = $_REQUEST['src'];

$script = file_get_contents($src);

header('Content-Type: text/javascript');
echo $script;
?>
