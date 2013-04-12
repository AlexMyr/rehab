<?php

class RestServiceBase {
  
  private $db;
  private $response;
  
  public function __construct()
  {
    $this->db = new DB();
    $this->response	= '';
  }
  
  public function getDB()
  {
    return $this->db;
  }
  
  public function setResponse($data)
  {
    $this->response = json_encode($data);
  }
  
  public function getResponse()
  {
    return $this->response;
  }
  
  public function process($method, $data)
  {  }
  
}

?>