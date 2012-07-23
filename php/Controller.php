<?php
abstract class Controller {
  
  protected abstract function execute();
  
  protected $response;
  protected $request;
  
  public function __construct() {
    $this->response = new stdClass;
    $this->request = G87::$request;
  } 
  
  public function getResponse($json=false) {
    $this->execute();
    
    if($json) return json_encode($this->response);
    
    return $this->response;
  }
}
?>