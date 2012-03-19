<?php
require_once("Facebook/facebook.php");

class FB {
  protected static $config = array(
    "appId" => FB_APP_ID,
    "secret" => _FB_APP_SECRET);
    
  protected static $obj = null;
  public static $user = null;
    
  public static function init() {
    $facebook = new Facebook(self::$config);
    self::$obj = $facebook;
    
    $user = self::fetchCurrentUser();
    if(!$user) return;
    
    self::$user = (object) array(
      "id" => $user->id,
      "name" => $user->name,
      "username" => $user->username);
  }
  
  public static function fetchCurrentUser() {
    $userId = self::getUserId();
    if($userId) {
      // Sometimes this user id might be an invalid one and might throw an exception.
      try {
        $user = self::$obj->api("/me", "GET");
        return $user;
      } catch (FacebookApiException $ex) {
        // No need to do anything as the below statement will anyhow return null.
      }
    }
    
    return null;
  }
  
  public static function getUser() {
    return self::$user;
  }
  
  public static function getUserId() {
    $userId = self::$obj->getUser();  // Facebook's getUser() method returns only user id.
    return $userId;
  }
}

FB::init();
?>