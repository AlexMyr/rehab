<?php
include_once('config.php');
include_once('db.class.php');
include_once('RestUtilsClass.php');
include_once('RestServiceBase.php');
include_once('RestServiceLogin.php');
include_once('RestServicePatients.php');
include_once('RestServiceExercises.php');


$service = trim($_SERVER['REDIRECT_URL'], '/');//preg_match('~\/(\w+)?\?~', $_SERVER['REQUEST_URI'], $tmp) ? $tmp[1] : '';
$resultService = null;
$resultCode = HTTP_RESPONSE_CODE_BAD_REQUEST;
$resultResponse = json_encode(array('status'=>'1', 'error'=>'Can not process request.'));

if($service)
{
  $data = RestUtils::processRequest();
  $resultService = null;
  switch($service)
  {
    case REST_SERVICE_LOGIN:
    {
      $resultService = new RestServiceLogin();
      $resultService->process($data->getMethod(), $data->getRequestVars());
      $resultCode = HTTP_RESPONSE_CODE_OK;
      $resultResponse = $resultService->getResponse();
    }
    break;
  
    case REST_SERVICE_PATIENTS:
    {
      $resultService = new RestServicePatients();
      $resultService->process($data->getMethod(), $data->getRequestVars());
      $resultCode = HTTP_RESPONSE_CODE_OK;
      $resultResponse = $resultService->getResponse();
    }
    break;
    
    case REST_SERVICE_EXERCISES:
    {
      $resultService = new RestServiceExercises();
      $resultService->process($data->getMethod(), $data->getRequestVars());
      $resultCode = HTTP_RESPONSE_CODE_OK;
      $resultResponse = $resultService->getResponse();
    }
    break;
  }
  RestUtils::sendResponse($resultCode, $resultResponse, 'application/json');
  
}
else
{
  
  RestUtils::sendResponse($resultCode, $resultResponse, 'application/json');
}

?>