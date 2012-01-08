<?php

if (!function_exists('getallheaders')) 
{
  function getallheaders() 
  {
    foreach ($_SERVER as $name => $value) 
    {
      if (substr($name, 0, 5) == 'HTTP_') 
      {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
}

$src = $_REQUEST['src'];
$requestHeaders = getallheaders();
$ch = curl_init($src);
$headers = array();

foreach($requestHeaders as $name => $value)
{
  $headers[] = "$name: $value";
}

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, 1); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$script = curl_exec($ch);
curl_close($ch);

header('Content-Type: text/javascript');
echo $script;
?>
