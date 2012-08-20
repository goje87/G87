<?php
$g87ScriptStartTime = microtime(true);
// set the log_errors flag to true and indicate to the system that it has to print the errors 
// in the file pointed by variable 'error_log'
ini_set("log_errors", "1");
ini_set("error_log" , "{$_SERVER['DOCUMENT_ROOT']}/G87/logs/php_log.txt");
error_reporting(E_ALL ^ E_NOTICE);

// start the sesstion
session_start();
date_default_timezone_set("Asia/Calcutta");

$G87DocumentRoot = $_SERVER['DOCUMENT_ROOT'];
$G87RequestUrl = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
// $G87RequestQuery = $_SERVER['QUERY_STRING'];
$G87RequestQuery = parse_url($G87RequestUrl, PHP_URL_QUERY);
$G87RequestPath = str_replace("?$G87RequestQuery", "", $G87RequestUrl);
$G87RequestPath = preg_replace("#\/$#", "", $G87RequestPath);

// Adding directories to include path.
$includeFolders = array(
  "$G87DocumentRoot/G87/php",
  "$G87DocumentRoot/G87/php/mustache",
  get_include_path());
set_include_path(implode(PATH_SEPARATOR, $includeFolders));

function autoload($className)
{
    @include "$className.php";
}
spl_autoload_register('autoload');

// Parse the siteConfig.json
G87::parseConfig("$G87DocumentRoot/G87/config/siteConfig.json");
$G87RequestRoot = G87_REQUEST_ROOT;

// Need to parse SCRIPT_URI to obtain the APP_KEY
$path = str_replace($G87RequestRoot, "", $G87RequestUrl);
preg_match('/^(\/)[A-Za-z0-9_\-]+/', $path, $matches);
$appKey = $matches[0];
$appKey = str_replace("/", "", $appKey);
$appDocumentRoot = "$G87DocumentRoot/$appKey";
$appRequestRoot = "$G87RequestRoot/$appKey";
$appRoute = str_replace($appRequestRoot, "", $G87RequestPath);



// Declaring global variables related to G87
//   G87_DOCUMENT_ROOT: Path to G87 directory from filesystem's prespective.
//   G87_REQUEST_ROOT: Path to G87 directory from URL prespective. (This will be read from siteConfig.js)
//   G87_REQUEST_URL: The URL to which the request was made (including query string).
//   G87_REQUEST_QUERY: The query string in the request.
//   G87_REQUEST_PATH: The URL to which the request was made minus query string.
define("G87_DOCUMENT_ROOT", $G87DocumentRoot);
define("G87_REQUEST_URL", $G87RequestUrl);
define("G87_REQUEST_QUERY", $G87RequestQuery);
define("G87_REQUEST_PATH", $G87RequestPath);

// Declaring global variables related to the app being accessed currently
//   APP_KEY: The unique key (name) of the app.
//   APP_DOCUMENT_ROOT: Path to the app's directory from filesystem's prespective.
//   APP_REQUEST_ROOT: Path to the app's directory from the URL prespective.
//   APP_ROUTE: The route in which the script can be found.
define("APP_KEY", $appKey);
define("APP_DOCUMENT_ROOT", $appDocumentRoot);
define("APP_REQUEST_ROOT", $appRequestRoot);
define("APP_ROUTE", $appRoute);

// For testing purpose: Print the values of the global constants defined
$constants = array(
  "G87_REQUEST_URL",
  "G87_REQUEST_PATH",
  "APP_REQUEST_ROOT",
  "APP_ROUTE",
  "G87_REQUEST_ROOT",
  "APP_KEY",
  "G87_REQUEST_QUERY",
  "G87_DOCUMENT_ROOT",
  "APP_DOCUMENT_ROOT");

foreach($constants as $constant) {
  Logger::debug("$constant: ".constant($constant));
}

// If this page is being hit due to a 404 redirect (from .htaccess file)
// query string will not be automatically parsed to $_REQUEST resulting in
// empty G87::$request. This function will parse it explicitly and populate
// G87::$request (only).
G87::parseQueryString(G87_REQUEST_QUERY);

// Parse the application's config file (if present).
G87::parseConfig(APP_DOCUMENT_ROOT."/appConfig.json");

// Execute the appInit.php (if present).
if(file_exists(APP_DOCUMENT_ROOT."/appInit.php")) include(APP_DOCUMENT_ROOT."/appInit.php");

// Try to route the request to the script file.
$path = Router::getPath(APP_ROUTE);
if($path) {
  Logger::debug("Gotta process $path");
  G87::render($path);
  $g87ScriptEndTime = microtime(true);
  $g87ScriptTotalTime = $g87ScriptEndTime - $g87ScriptStartTime;
  Logger::debug("Total time for execution of script: $g87ScriptTotalTime seconds");
  exit; 
}

?>
