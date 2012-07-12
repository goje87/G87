<?php
class InstallationStep
{
	public static $steps = array();
	function __construct()
	{
    self::$steps[] = $this;
	}
  public $message;
  public $execute;
}
?>