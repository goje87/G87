<?php
class G87View {
  
  public static function init() {
    require G87_DOCUMENT_ROOT."/G87/php/mustache/src/Mustache/Autoloader.php";
    Mustache_Autoloader::register();
    
    self::$helpers = array(
      "include" => function($content) {
        $path = Router::getPath($content);
        $viewProcessor = new G87View($path);
        return $viewProcessor->process();
      },
      
      "const" => function($content) {
        return constant($content);
      },
      
      "pageTitle" => function($content) {
        G87::setPageTitle($content);
        return "";
      },
      "stylesheet" => function($content) {
        G87::addStylesheet($content);
        return "";
      },
      "script" => function($content) {
        G87::addScript($content);
        return "";
      });
  }
  
  protected $path;
  protected $template;
  protected $controller;
  protected $data;
  protected $response;
  protected static $helpers;
  
  public function __construct($path, $data = array()) {
    $this->path = $path;
    $this->data = (array) $data;
  }
  
  public function addHelper($name, $handler) {
    self::$helpers[$name] = $handler;
  }
  
  public function process() {
    if(!$this->path) return "";
    
    $this->template = $this->getTemplate($this->path);
    $this->controller = $this->getController($this->template);
    $this->data = array_merge($this->data, (array)$this->getData($this->controller));
    $this->response = $this->getResponseString($this->template, $this->data);
    
    return $this->response;
  }
  
  protected function getTemplate($path) {
    return file_get_contents($path);
  }
  
  protected function getController($template) {
    $path = $this->getControllerPath($template);
    if(!$path || !file_exists($path)) return;
    
    // include_once($path);
    // $controllerClass = $this->getControllerClassName($path);
    // return new $controllerClass;
    return G87::getControllerFromPath($path);
  }
  
  protected function getControllerPath($template) {
    $tokenizer = new Mustache_Tokenizer;
    $parser = new Mustache_Parser;
    
    $tokens = $tokenizer->scan($template);
    $tree = $parser->parse($tokens);
    
    $firstNode = $tree[0];
    if($firstNode['type'] == "#" && $firstNode['name'] == "controller") return Router::getPath($firstNode['nodes'][0]['value']);
  }
  
  // protected function getControllerClassName($path) {
    // preg_match("@\/([^/^\.]+)\.[^/^\.]+$@i", $path, $matches);
    // return ucfirst($matches[1])."Controller";
  // }
  
  protected function getData($controller) {
    if(!$controller) return "";
    return $controller->getResponse();
  }
  
  protected function getResponseString($template, $data = array()) {
    $m = new Mustache_Engine(array(
      "helpers" => self::$helpers));
    return $m->render($template, $data);
  }
  
  public static function quickRender($template, $data = array()) {
    $m = new Mustache_Engine(array(
      "helpers" => self::$helpers));
    return $m->render($template, $data);
  }
  
}

G87View::init();
?>