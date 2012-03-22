<?php 
class G87 {
  public static $request;
  public static $response;
  public static function respond($data) {
    self::$response = $data;
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
}
?>
