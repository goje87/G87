<?php

class Utils
{
  public static $silentMode = false; 
  public static function printLine($str)
  {
    if(self::$silentMode)
      return;

    echo '<pre>';
    echo $str;
    echo '</pre>';
    echo '<hr />';
  }

  public static function printR($data)
  {
    if(self::$silentMode)
      return;

    echo '<pre>';
    print_r($data);
    echo '</pre>';
  }

  public static function output($data)
  {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
  }
  
  public static function outputResponse($data, $type="json")
  {
    switch($type)
    {
      case "json":
        echo json_encode($data);
        break;
    }
  }
  
  
  // *** DEPRICATED: There is an in-built function in PHP in_array() 
  public static function inArray($value, $array) {
    if(array_search($value, $array) === false) return false;
    
    return true;
  }
  
  public static function appendToIncludePath($pathData) {
    if(is_string($pathData)) $paths = array($pathData);
    if(is_array($pathData)) $paths = $pathData;
    
    $paths[] = get_include_path();
    
    set_include_path(implode(PATH_SEPARATOR, $paths));
  }
  
  public static function getAbsoluteUrl($mainUrl, $relativeUrl) {
    if($relativeUrl{0} != "\\" && $relativeUrl{0} != "/") {
      return "$mainUrl/$relativeUrl";
    }
    
    $parts = parse_url($mainUrl);
    $base = "{$parts['scheme']}://{$parts['host']}";
    return "$base$relativeUrl";
  }

  public static function trimSpaces($string) {
    $tString = preg_replace('/\s+/', ' ', $string);
    
    return trim($tString); 
  }
}

?>
