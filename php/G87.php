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
  
  public static function makeRequest($url, $config = null) {
    if(!$config) {
      $config = new stdClass();
    }
    
    $c = curl_init($url);
    
    if($config->type == "POST") {
      curl_setopt($c, CURLOPT_POST, true);
      curl_setopt($c, CURLOPT_POSTFIELDS, $config->params);
    }
    
    curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($c);
    curl_close($c);
    
    return $response;
  }
}
?>
