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
  
  public static function inArray($value, $array) {
    if(array_search($value, $array) === false) return false;
    
    return true;
  }
}

?>
