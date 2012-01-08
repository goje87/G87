<?php
$request = (object) $_REQUEST;

$url = $request->url;
if(!$url) return;

$data = file_get_contents($url);

echo $data;
?>