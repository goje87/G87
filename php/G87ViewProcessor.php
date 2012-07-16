<?php
class G87ViewProcessor {
  
  public static function init() {
    require G87_DOCUMENT_ROOT."/G87/php/mustache/src/Mustache/Autoloader.php";
    Mustache_Autoloader::register();
  }
  
  protected $path;
  protected $template;
  protected $controller;
  protected $data;
  protected $response;
  
  public function __construct($path) {
    $this->path = $path;
  }
  
  public function process() {
    if(!$this->path) return "";
    
    $this->template = $this->getTemplate($this->path);
    $this->controller = $this->getController($this->template);
    $this->data = $this->getData($this->controller);
    $this->response = $this->getResponseString($this->template, $this->data);
    
    return $this->response;
  }
  
  protected function getTemplate($path) {
    return file_get_contents($path);
  }
  
  protected function getController($template) {
    $path = $this->getControllerPath($template);
    if(!$path || !file_exists($path)) return;
    
    include_once($path);
    $controllerClass = $this->getControllerClassName($path);
    return new $controllerClass;
  }
  
  protected function getControllerPath($template) {
    $tokenizer = new Mustache_Tokenizer;
    $parser = new Mustache_Parser;
    
    $tokens = $tokenizer->scan($template);
    $tree = $parser->parse($tokens);
    
    $firstNode = $tree[0];
    if($firstNode['type'] == "#" && $firstNode['name'] == "controller") return APP_DOCUMENT_ROOT.$firstNode['nodes'][0]['value'];
  }
  
  protected function getControllerClassName($path) {
    preg_match("@\/([^/^\.]+)\.[^/^\.]+$@i", $path, $matches);
    return ucfirst($matches[1])."Controller";
  }
  
  protected function getData($controller) {
    if(!$controller) return "";
    return $controller->getResponse();
  }
  
  protected function getResponseString($template, $data) {
    $m = new Mustache_Engine(array(
      "helpers" => array(
        "bold" => function($text) {
          return "<b>$text</b>";
        },
        "include" => function($text) {
          $path = Router::getPath($text);
          $viewProcessor = new G87ViewProcessor($path);
          return $viewProcessor->process();
        })));
    return $m->render($template, $data);
  }
  
}

G87ViewProcessor::init();
?>