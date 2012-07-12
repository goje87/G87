<?php
class Logger
{
  public static $outputFile;
  const OUTPUT_TYPE_ERROR = 0;
  const OUTPUT_TYPE_INFO = 1;
  const OUTPUT_TYPE_DEBUG = 2;
  public static $outputLevel = 2;
  

  protected static function _log($msg, $type = self::OUTPUT_TYPE_INFO)
  {
    // First check the output type is within $outputLevel
	  if($type > self::$outputLevel) return;
    
    $outputFile = (self::$outputFile)?(self::$outputFile):"{$_SERVER['DOCUMENT_ROOT']}/G87/logs/php_log.txt";
	
    $file = fopen($outputFile, 'a');
    $time = date("d-m-Y H:i:s");
    
    $trace = debug_backtrace();
    $caller = array_shift($trace);
    $caller = array_shift($trace);
    
    $line = $caller['line'];
    $caller2 = array_shift($trace);
    $function = $caller2['function'];
    $class = @$caller2['class'];
	
    $msg = is_string($msg)?$msg:print_r($msg, true);
	
    switch($type)
    {
      case self::OUTPUT_TYPE_ERROR:
        $outputType = "ERROR";
        break;
      
      case self::OUTPUT_TYPE_INFO:
        $outputType = "INFO";
        break;
      
      case self::OUTPUT_TYPE_DEBUG:
        $outputType = "DEBUG";
        break;
    }
	
    $output = "[$class->$function()](line: $line) $outputType: $msg";
    
	error_log($output);
    // fwrite($file, $output);
    fclose($file);
  }
  
  public static function info($msg)
  {
    self::_log($msg, self::OUTPUT_TYPE_INFO);
  }
  
  public static function error($msg)
  {
    self::_log($msg, self::OUTPUT_TYPE_ERROR);
  }
  
  public static function debug($msg)
  {
    self::_log($msg, self::OUTPUT_TYPE_DEBUG);
  }
}
?>
