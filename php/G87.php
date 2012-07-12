<?php 
class G87 {
  public static $request;
  
  public static function init() {
    self::$request = (object) $_REQUEST;
  }
  
  public static function respond($data) {
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
  
  public static function render($path) {
    preg_match("/\.([a-zA-Z0-9]+)$/", $path, $matches);
    $extension = $matches[1];
    switch ($extension) {
      case 'view':
        $viewProcessor = new G87ViewProcessor($path);
        $response = $viewProcessor->process();
        G87::respond($response);
        break;
      case 'controller':
        break;
      case 'php':
        include($path);
      case 'html':
        G87::respond(file_get_contents($path));
      default:
        echo "unable to render file of type $extension...";
        break;
    }
    
  }
}

G87::init();
?>
