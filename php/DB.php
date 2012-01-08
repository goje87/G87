<?php
class DB
{
	/*
   * DB::$servers (protected)
   * Holds the list of servers that were created using DB::serServer()
   */ 
	protected static $servers = array();
  
  /*
   * DB::SERVER_KEY_DEFAULT
   * The default server key
   */
  const SERVER_KEY_DEFAULT = "default";
  
  /*
   * DB::$lastInsertId
   * Contains the ID of most recently inserted row (same as mysql_insert_id()).
   */
  public static $lastInsertId;
  
  /*
   * DB::init() (for internal use only) 
   * This function is meant to be called whenever DB.php is loaded.
   * This function deals with initialization of the DB module. It sets a default
   * server with globals DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE.
   * 
   * NOTE: Do not call this function from your code.
   */
  public static function init() {
    $config = new stdClass();
    $config->host = DB_HOST;
    $config->username = DB_USERNAME;
    $comfig->password = DB_PASSWORD;
    $config->database = DB_DATABASE;
    self::setServer($config);
  }
  
  /*
   * DB::setServer($config, $key=DB::SERVER_KEY_DEFAULT)
   * Adds a database server to the list of server that can be refrenced with the $key.
   * If no key is passed, then the server is added with key 'config'.
   * 
   * Input: 
   *   (object) $config: An object that contains server configuration.
   *     (string) $config->host: Database host.
   *     (string) $config->username: Username used to login the database server.
   *     (string) $config->password: Password used to login the database server.
   *     (string) $config->database: The database to be selected.
   *   (string) $key: The key to identify the server being set.
   */
  public static function setServer($config, $key=self::SERVER_KEY_DEFAULT)
  {
  	self::$servers[$key] = $config;
  }
  
  /*
   * DB::getServer($key=DB::SERVER_KEY_DEFAULT)
   * Returns the config object for the server refrenced with $key.
   * 
   * Input:
   *   (string) $key: The key for which the config object is required.
   * 
   * Output: 
   *   (object) The config object.
   */
  public static function getServer($key=self::SERVER_KEY_DEFAULT)
  {
  	return self::$servers[$key];
  }
  
  /*
   * DB::execQuery($query, $multiple=false)
   * Executes $query on the DB server. $multiple needs to be set to true if $query consists of
   * multiple SQL statements.
   * 
   * Input: 
   *   (string) $query: The query/queries to be executed.
   *   (boolean) $multiple: Indicate whether $query consists multiple SQL statements.
   *   
   * Return: 
   *   (array) Array of row objects. OR
   *   (stdClass) An object like $result->error as true and $result->message holding the 
   *     error.
   */
  public static function execQuery1($query, $multiple=false, $key=self::SERVER_KEY_DEFAULT)
  {
  	$server = self::getServer($key);
    $link = mysql_connect($server->host, $server->username, $server->password, false, ($multiple)?65536:0);
    if($link)
    {
      if(mysql_select_db($server->database, $link))
      {
        $data = @mysql_query($query, $link);
        if($data)
        {
          $result = array();
          while($row = @mysql_fetch_object($data))
          {
            $result[] = $row;
          }
          
          self::$lastInsertId = mysql_insert_id($link);
          
          mysql_close($link);
          return $result;
        }
      }
    }
    
    $result = new stdClass();
    $result->error = true;
    $result->message = mysql_error($link);
    mysql_close($link);
    return $result;
  }
  
  public static function execQuery($query, $multiple=false, $key=self::SERVER_KEY_DEFAULT)
  {
  	$server = self::getServer($key);
    $link = mysqli_connect($server->host, $server->username, $server->password, $server->database);
    
    if($link)
    {
      if(mysqli_multi_query($link, $query))
      {
        Logger::debug('here');
      	$result = array();
      	$resultset = mysqli_store_result($link);
      	if($resultset)
      	{
      		while($row = mysqli_fetch_object($resultset))
	      	{
	      		$result[] = $row;
      		}
      	}
      	
      	self::$lastInsertId = mysqli_insert_id($link);
      	
      	mysqli_close($link);
      	return $result;
      }
    }
    
    $result = new stdClass();
    $result->error = true;
    $result->message = mysqli_error($link);
    mysqli_close($link);
    return $result;
  }
  
  /*
   * DB::escapeString($string)
   * Escapes the $string that is to be used in the SQL query.
   * Input:
   *   (string) $string: The string that is to be escaped.
   *   
   * Returns: (string) The escaped string.
   */
  
  public static function escapeString($string)
  {
    $search=array("\\","\0","\r\n","\n","\r","\x1a","'",'"');
    $replace=array("\\\\","\\0","\\r\\n","\\n","\\r","\Z","\'",'\"');
    return str_replace($search,$replace,$string);
  }
}

DB::init();

?>