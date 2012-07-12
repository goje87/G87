<?php
require_once('Utils.php');

class GFC
{
  public static function getViewerId()
  {
    // If the user is not signed in, return a blank string
    if(!self::isUserSignedIn())
    {
      return '';
    }
    
    if(isset($_SESSION['viewer']))
    {
      $viewer = $_SESSION['viewer'];
    }
    else
    {
      $fcauth = self::getFcauthContents();
      $viewerData = file_get_contents("http://www.google.com/friendconnect/api/people/@viewer/@self?fcauth=$fcauth");    
      $viewer = json_decode($viewerData);
      $_SESSION['viewer'] = $viewer;
    }
    
    return $viewer->entry->id;
  }
  
  public static function getUserFromId($userId)
  {
    /*$fcauth = self::getFcauthContents(); 
    $userData = file_get_contents("http://www.google.com/friendconnect/api/people/$userId/@self?fcauth=$fcauth");    
    $user = json_decode($userData);
    return $user;*/
    
    include_once('OAuthStore.php');
    include_once('OAuthRequester.php');
    
    echo ' - starting to fetch the user data';
    
    $key = '*:13900013409698565095'; // this is your consumer key
    $secret = '1a-78y07dT4='; // this is your secret key

    $options = array( 'consumer_key' => $key, 'consumer_secret' => $secret );
    OAuthStore::instance("2Leg", $options );

    echo ' - assigned the user key and secret';
    
    $url = "https://www.google.com/friendconnect/api/people/$userId/@self"; // this is the URL of the request
    $method = "GET"; // you can also use POST instead
    $params = null;

    try
    {
      echo ' - and here we go';
      // Obtain a request object for the request we want to make
      $request = new OAuthRequester($url, $method, $params);

      // Sign the request, perform a curl request and return the results, 
      // throws OAuthException2 exception on an error
      // $result is an array of the form: array ('code'=>int, 'headers'=>array(), 'body'=>string)
      $result = $request->doRequest();
      
      echo ' - the result is ';
      print_r($result); 
      $response = $result['body'];
      return $response;
    }
    catch(OAuthException2 $e)
    {
      echo ' - oops there was an exception ';
      print_r($e);

    }
  }
  
  public static function isUserSignedIn()
  {
    $cookieName = 'fcauth'.GFC_SITE_ID;
    
    return isset($_COOKIE[$cookieName]);
  }
  
  private static function getFcauthContents()
  {
    if(!self::isUserSignedIn())
      return;
      
    $cookieName = 'fcauth'.GFC_SITE_ID;
    
    return $_COOKIE[$cookieName];
  }
}
?>