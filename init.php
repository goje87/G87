<?php

// set the log_errors flag to true and indicate to the system that it has to print the errors 
// in the file pointed by variable 'error_log'
ini_set("log_errors", "1");
ini_set("error_log" , "{$_SERVER['DOCUMENT_ROOT']}/G87/logs/php_log.txt");
error_reporting(E_ALL ^ E_NOTICE);

// start the sesstion
session_start();

// Declaring global variables related to G87
//   G87_DOCUMENT_ROOT: Path to G87 directory from filesystem's prespective.
//   G87_SERVER_ROOT: Path to G87 directory from URL prespective. (This will be read from siteConfig.js)
//   SCRIPT_URI: The URL to which the request was made.
define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']); // DEPRICATED LINE
define("G87_DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);
define("SCRIPT_URI", "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");

// Adding directories to include path.
$documentRoot = G87_DOCUMENT_ROOT;
$includeFolders = array(
  "$documentRoot/G87/php",
  "$documentRoot/G87/php/oauth-php/library",
  get_include_path());
set_include_path(implode(PATH_SEPARATOR, $includeFolders));


// Parse the siteConfig.json
G87::parseConfig(G87_DOCUMENT_ROOT."/G87/config/siteConfig.json"); 


// Declaring global variables related to the app being accessed currently
//   APP_KEY: The unique key (name) of the app.
//   APP_DOCUMENT_ROOT: Path to the app's directory from filesystem's prespective.
//   APP_SERVER_ROOT: Path to the app's directory from the URL prespective.

// Need to parse SCRIPT_URI to obtain the APP_KEY
$path = str_replace(G87_SERVER_ROOT, "", SCRIPT_URI);
preg_match('/^(\/)?[A-Za-z0-9_\-]+/', $path, $matches);
$app = $matches[0];
$app = str_replace("/", "", $app);
define("APP_KEY", $app);
define("APP_DOCUMENT_ROOT", G87_DOCUMENT_ROOT."/".APP_KEY);
define("APP_SERVER_ROOT", G87_SERVER_ROOT."/".APP_KEY);

G87::parseConfig(APP_DOCUMENT_ROOT."/appConfig.json");

// Execute the appInit.php (if present).
if(file_exists(APP_DOCUMENT_ROOT."/appInit.php")) include(APP_DOCUMENT_ROOT."/appInit.php");





function __autoload($className)
{
  include "$className.php";
}

// function parseConfig($filepath)
// {
  // if(!file_exists($filepath)) return;
//   
  // $json = file_get_contents($filepath);
  // $config = json_decode($json);
  // foreach($config as $key => $value)
  // {
    // define($key, $value);
  // }
// }

?>
