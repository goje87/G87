<?php
class Router {
  
  protected static $extensions = array(
    "view",
    "controller",
    "php",
    "html");
    
  public static function getPath($route) {
    $route = self::translateFromRouteMap($route);
    
    // Generate path
    $path = APP_DOCUMENT_ROOT.$route;
    
    if(is_file($path))  return $path;
    if(is_dir($path)) {
      $path .= "/index";
    }
    
    foreach(self::$extensions as $extension) {
      $searchPath = "$path.$extension";
      if(is_file($searchPath)) return $searchPath;
    }
    
    return ;
  }
  
  protected static function translateFromRouteMap($route) {
    $routesJson = Utils::getFileContents(APP_DOCUMENT_ROOT."/routes.json");
    if(!$routesJson) return $route;
    
    $routes = json_decode($routesJson);
    
    $patternKeys = array();
    $patternReplacements = array();
    
    foreach(get_object_vars($routes->patterns) as $key => $regEx) {
      $patternKeys[] = "[$key]";
      $patternReplacements[] = "($regEx)";
    }
    
    foreach(get_object_vars($routes->map) as $from => $to) {
      $from = str_replace($patternKeys, $patternReplacements, $from);
      if(preg_match("#^$from$#", $route)) {
        $translated = preg_replace("#^$from$#", $to, $route);
        $newRouteData = explode("?", $translated);
        $newRoute = $newRouteData[0];
        if($newRouteData[1]) {
          G87::parseQueryString($newRouteData[1]);
        }
        return $newRoute;
      }
    }
    
    return $route;
  }
}
?>