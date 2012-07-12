<?php
class Setup {
  public $step = 1;
  public $hideNextButton = false;
  
  public function preInit() {
    $request = (object)$_REQUEST;
    
    $this->step = isset($request->step)?$request->step:1;
    $this->app = isset($request->app)?$request->app:'G87';
    $this->defineGlobals();
    
    $documentRoot = G87_DOCUMENT_ROOT."/$this->app";
    $this->setupDir = "$documentRoot/setup";
    G87::parseConfig("$documentRoot/appConfig.json");
  }
  
  protected function defineGlobals() {
    if(!defined(G87_DOCUMENT_ROOT)) {
      $script_filename = $_SERVER['SCRIPT_FILENAME'];
      $g87_document_root = preg_replace('/\/G87\/setup\.php/', '', $script_filename);
      define("G87_DOCUMENT_ROOT", $g87_document_root);
    }
    
    if(!defined(SCRIPT_URI)) define("SCRIPT_URI", "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
  }
  
  public function init() {
    $request = (object) $_REQUEST;
    
    if(!$request->submit) return;
    
    $this->step = new Step($this);
    $this->step->execute();
  }
  
  public function nextStep() {
    $this->goToStep($this->step + 1);
  }
  
  public function finish() {
    $this->goToStep("finish");
  }
  
  public function goToStep($step) {    
    $redirectUrl = $this->getRedirectUrl(SCRIPT_URI, array("step" => $step, "app" => $this->app));
    
    echo "Please Wait...<script language=\"javascript\">window.location = '$redirectUrl';</script>";
  }
  
  protected function getRedirectUrl($url, $params) {
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
  
  public function hideNextButton() {
    $this->hideNextButton = true;
  }
}
?>