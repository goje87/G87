<?php

/* Syntax: execQuery(string $query, string $database)
 * 
 * This procedure executes the $query on $database. It returns
 * the resource $data if successful. Else it returns error
 * message.
*/
function execQuery($query, $database)
{
  include 'dbconfig.php';
  mysql_connect($_db_server,$_db_username,$_db_password) or die(mysql_error());
  mysql_select_db($database) or die(mysql_error());
  
  $data = mysql_query($query) or die(mysql_error());
  
  return $data;
  
  mysql_close();
}
 
?>