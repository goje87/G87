<?php
class Router {
  
  public static function getPath($route) {
    // Generate path
    $path = APP_DOCUMENT_ROOT.$route;
    
    if(is_dir($path)) {
      
      // Search for index.view
      $path .= "/index.view";
      if(is_file($path)) return $path;
    }
    else {
      $pattern = "@\/(([a-zA-Z0-9_\-.])+$)@";
      preg_match($pattern, $path, $matches);
      $endPoint = $matches[1];
      $path = preg_replace($pattern, "", $path);
      
      $searchPath = "$path/$endPoint.view";
      if(is_file($searchPath)) return $searchPath;
      
      $searchPath = "$path/$endPoint.php";
      if(is_file($searchPath)) return $searchPath;
    }
    
    return ;
  }
}
?>