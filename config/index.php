<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/G87/init.php");

$request = (object) $_REQUEST;
$file = DOCUMENT_ROOT.$_SERVER['REDIRECT_URL'];
$fileContent = file_get_contents($file);

if($request->js)  // A request for a javascipt file is being made.
{
	$ctype = "text/javascript";
	$fileParts = explode("/", $file);
	$filename = $fileParts[count($fileParts) - 1];
	$var = preg_replace("@(.+)(\.json)@","$1", $filename);
	$content = "jQuery.extend(window, $fileContent);";
}
else  // A request for JSON is being made
{
	$ctype = "application/json; charset=UTF-8";
	$content = $fileContent;
}

header("Content-type: $ctype");
echo $content;
?>