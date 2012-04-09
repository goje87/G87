<?php
class Setup {
  public static $step = 1;
  public static $app = "";
  public static $hideNextButton = false;
  public static $setupDir = "";
  protected static $oStep = null;
  
  public static function preInit() {
    $request = (object)$_REQUEST;
    
    self::$step = isset($request->step)?$request->step:1;
    self::$app = isset($request->app)?$request->app:'G87';
    self::defineGlobals();
    
    $documentRoot = G87_DOCUMENT_ROOT."/".self::$app;
    self::$setupDir = "$documentRoot/setup";
    G87::parseConfig("$documentRoot/appConfig.json");
  }
  
  protected static function defineGlobals() {
    if(!defined(G87_DOCUMENT_ROOT)) {
      $script_filename = $_SERVER['SCRIPT_FILENAME'];
      $g87_document_root = preg_replace('/\/G87\/setup\.php/', '', $script_filename);
      define("G87_DOCUMENT_ROOT", $g87_document_root);
    }
    
    if(!defined(SCRIPT_URI)) define("SCRIPT_URI", "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
  }
  
  public static function init() {
    $request = (object) $_REQUEST;
    
    if(!$request->submit) return;
    
    self::$oStep = new Step();
    self::$oStep->execute();
  }
  
  public static function nextStep() {
    self::goToStep(self::$step + 1);
  }
  
  public static function finish() {
    self::goToStep("finish");
  }
  
  public static function goToStep($step) {    
    $redirectUrl = self::getRedirectUrl(SCRIPT_URI, array("step" => $step, "app" => self::$app));
    
    echo "Please Wait...<script language=\"javascript\">window.location = '$redirectUrl';</script>";
  }
  
  protected static function getRedirectUrl($url, $params) {
    // $pairs = array();
    // foreach($params as $key => $value) {
      // $pairs[] = "$key=".urlencode($value);
    // }
    // $queryString = implode("&", $pairs);
    
    $queryString = http_build_query($params);
    
    $urlParts = parse_url($url);
    $redirectUrl = "{$urlParts['scheme']}://{$urlParts['host']}{$urlParts['path']}?$queryString";
    return $redirectUrl;
  }
  
  public static function hideNextButton() {
    self::$hideNextButton = true;
  }
}
?>