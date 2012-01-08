<?php

ini_set("log_errors", "1");
ini_set("error_log" , "{$_SERVER['DOCUMENT_ROOT']}/G87/logs/php_log.txt");
error_reporting(E_ALL ^ E_NOTICE);
session_start();
define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);
define("SCRIPT_URI", "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");

$documentRoot = DOCUMENT_ROOT;

$includeFolders = array(
  "$documentRoot/G87/php",
  "$documentRoot/G87/php/oauth-php/library",
  "$documentRoot/t",
  get_include_path());
set_include_path(implode(PATH_SEPARATOR, $includeFolders));


// Parse the siteConfig.json
parseConfig("$documentRoot/G87/config/siteConfig.json");

// Get the path for appConfig.json
$path = str_replace(G87_SERVER_ROOT, "", SCRIPT_URI);
preg_match('/^(\/)?[A-Za-z0-9_\-]+/', $path, $matches);
$app = $matches[0];
$app = str_replace("/", "", $app);

$appRoot = "$documentRoot/$app";
define("APP_ROOT", $appRoot);
parseConfig("$documentRoot/$app/appConfig.json");
if(file_exists("$appRoot/appInit.php")) include("$appRoot/appInit.php");

function __autoload($className)
{
  include "$className.php";
}

function parseConfig($filepath)
{
  if(!file_exists($filepath)) return;

  $json = file_get_contents($filepath);
  $config = json_decode($json);
  foreach($config as $key => $value)
  {
    define($key, $value);
  }
}

?>
