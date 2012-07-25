<?php 
class G87 {
  const RESPONSE_200 = "response200";
  const RESPONSE_404 = "response404";
  
  public static $request;
  protected static $stylesheets = array();
  protected static $scripts = array();
  protected static $pageTitle = "";
  
  public static function init() {
    self::$request = (object) $_REQUEST;
  }
  
  public static function respond($data, $type = self::RESPONSE_200) {
    switch($type) {
      case self::RESPONSE_200: $code = "200 OK"; break;
      case self::RESPONSE_404: $code = "404 Not Found"; break;
      default:
      case self::RESPONSE_500: $code = "500 Internal Server Error"; break;
    }
    
    header("HTTP/1.0 $code");
    header("Status: $code");
    echo $data;
  }
  
  public static function parseConfig($filepath) {
    if(!file_exists($filepath)) return;
  
    $json = file_get_contents($filepath);
    $config = json_decode($json);
    foreach($config as $key => $value)
    {
      define($key, $value);
    }
  }
  
  public static function render($path, $type = self::RESPONSE_200) {
    preg_match("/\.([a-zA-Z0-9]+)$/", $path, $matches);
    $extension = $matches[1];
    switch ($extension) {
      case 'view':
        $response = self::renderView($path);
        G87::respond($response, $type);
        break;
      case 'controller':
        $controller = self::getControllerFromPath($path);
        $response = $controller->getResponse(true);
        G87::respond($response, $type);
        break;
      case 'php':
        include($path);
        break;
      case 'html':
        G87::respond(file_get_contents($path), $type);
      default:
        G87::respond("unable to render file of type $extension...", self::RESPONSE_500);
        break;
    }
    
  }
  
  protected static function renderView($path) {
    self::addStylesheet(G87_REQUEST_ROOT."/G87/js/basic.css");
    self::addScript(G87_REQUEST_ROOT."/G87/js/basic.js");
    
    $view = new G87View($path);
    $viewResponse = $view->process();
    
    $gViewPath = Router::getPath("/gTemplate.view");
    $gView = new G87View($gViewPath, array("mainContent" => $viewResponse));
    $gViewResponse = $gView->process();
    
    $hViewPath = G87_DOCUMENT_ROOT."/G87/php/webPage.view";
    $hView = new G87View($hViewPath, array(
      "body" => $gViewResponse,
      "stylesheets" => self::$stylesheets,
      "scripts" => self::$scripts,
      "pageTitle" => self::$pageTitle));
    return $hView->process();
  }
  
  public static function addStylesheet($path) {
    $path = G87View::quickRender($path);
    self::$stylesheets[] = $path;
  }
  
  public static function addScript($path) {
    $path = G87View::quickRender($path);
    self::$scripts[] = $path;
  }
  
  public static function setPageTitle($title) {
    $title = G87View::quickRender($title);
    self::$pageTitle = $title;
  }
  
  public static function parseQueryString($queryString) {
    parse_str($queryString, $pairs);
    foreach($pairs as $key => $value) {
      G87::$request->$key = urldecode($value);
    }
  }
  
  public static function getControllerFromPath($path) {
    if(!$path || !file_exists($path)) return;
    
    include_once($path);
    preg_match("@\/([^/^\.]+)\.[^/^\.]+$@i", $path, $matches);
    $controllerClass = ucfirst($matches[1])."Controller";
    
    return new $controllerClass;
  }
}

G87::init();
?>
