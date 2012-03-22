<?php
//require_once("{$_SERVER['DOCUMENT_ROOT']}/G87/init.php");

$request = (object) $_REQUEST;
$file = G87_DOCUMENT_ROOT.$_SERVER['REDIRECT_URL'];
$fileContent = file_get_contents($file);
$fileContent = removeSecretPairs($fileContent);

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

function removeSecretPairs($json) {
  $obj = json_decode($json);
  foreach($obj as $key => $value) {
    if(preg_match("/^[_](.*)/", $key)) unset($obj->$key);
  }
  
  return json_encode($obj);
}

header("Content-type: $ctype");
echo $content;
?>