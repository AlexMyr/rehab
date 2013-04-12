<?php

class Trainer {
  public $trainer_id;
  public $username;
  public $company_name;
  public $first_name;
  public $surname;
  public $address;
  public $city;
  public $postcode;
  public $email;
  public $website;
  public $phone;
  public $mobile;
  public $fax;
  public $header_logo;
  public $himage_pos;
  
  public function __construct()
  {
    $this->setId(0);
    $this->setUsername('');
    $this->setCompanyName('');
    $this->setName('');
    $this->setSurname('');
    $this->setAddress('');
    $this->setCity('');
    $this->setPostcode('');
    $this->setEmail('');
    $this->setWebsite('');
    $this->setPhone('');
    $this->setMobile('');
    $this->setFax('');
    $this->setHeaderLogo('');
    $this->setHimagePos('');
  }
  
  static function createTrainerById($trainer_id, $dbCon)
  {
    if($trainer_id)
    {
      $trainer = null;
      $query_str = "select * from trainer t left join trainer_header_paper thp on thp.trainer_id = t.trainer_id where t.trainer_id = ".mysql_real_escape_string($trainer_id);
      if($row = $dbCon->getRow($query_str))
      {
        $row['header_logo'] = $row['logo_image'] ? DOMAIN_NAME.'/upload/'.$row['logo_image'] : DOMAIN_NAME.'/tcpdf/images/pdfheader.jpg';
        $trainer = Trainer::createTrainer(
                                          $trainer_id, $row['username'], $row['company_name'], $row['first_name'],
                                          $row['surname'], $row['address'], $row['city'], $row['post_code'], $row['email'], $row['website'],
                                          $row['phone'], $row['mobile'], $row['fax'], $row['header_logo'], $row['himage_pos']
                                          );
      }
      else
      {
        $trainer = new Trainer();
      }
      return $trainer;
    }
    return null;
  }
  
  static function createTrainer(
                                $trainer_id, $username, $company_name, $first_name,
                                $surname, $address, $city, $postcode, $email, $website,
                                $phone, $mobile, $fax, $header_logo, $himage_pos
                                )
  {
    $instance = new self();
    $instance->setId($trainer_id);
    $instance->setUsername($username);
    $instance->setCompanyName($company_name);
    $instance->setName($first_name);
    $instance->setSurname($surname);
    $instance->setAddress($address);
    $instance->setCity($city);
    $instance->setPostcode($postcode);
    $instance->setEmail($email);
    $instance->setWebsite($website);
    $instance->setPhone($phone);
    $instance->setMobile($mobile);
    $instance->setFax($fax);
    $instance->setHeaderLogo($header_logo);
    $instance->setHimagePos($himage_pos);
    return $instance;
  }
  
  public function setId($trainer_id){
    $this->trainer_id = $trainer_id;
  }
  public function getId(){
    return $this->trainer_id;
  }
  
  public function setUsername($username){
    $this->username = $username;
  }
  public function getUsername(){
    return $this->username;
  }
  
  public function setCompanyName($company_name){
    $this->company_name = $company_name;
  }
  public function getCompanyName(){
    return $this->company_name;
  }
  
  public function setName($first_name){
    $this->first_name = $first_name;
  }
  public function getName(){
    return $this->first_name;
  }
  
  public function setSurname($surname){
    $this->surname = $surname;
  }
  public function getSurname(){
    return $this->surname;
  }
  
  public function setAddress($address){
    $this->address = $address;
  }
  public function getAddress(){
    return $this->address;
  }
  
  public function setCity($city){
    $this->city = $city;
  }
  public function getCity(){
    return $this->city;
  }
  
  public function setPostcode($postcode){
    $this->postcode = $postcode;
  }
  public function getPostcode(){
    return $this->postcode;
  }
  
  public function setEmail($email){
    $this->email = $email;
  }
  public function getEmail(){
    return $this->email;
  }
  
  public function setWebsite($website){
    $this->website = $website;
  }
  public function getWebsite(){
    return $this->website;
  }
  
  public function setPhone($phone){
    $this->phone = $phone;
  }
  public function getPhone(){
    return $this->phone;
  }
  
  public function setMobile($mobile){
    $this->mobile = $mobile;
  }
  public function getMobile(){
    return $this->mobile;
  }
  
  public function setFax($fax){
    $this->fax = $fax;
  }
  public function getFax(){
    return $this->fax;
  }
  
  public function setHeaderLogo($header_logo){
    $this->header_logo = $header_logo;
  }
  public function getHeaderLogo(){
    return $this->header_logo;
  }
  
  public function setHimagePos($himage_pos){
    $this->himage_pos = $himage_pos;
  }
  public function getHimagePos(){
    return $this->himage_pos;
  }
  
}

class RestServiceLogin extends RestServiceBase {
  private $status;
  private $message;
  private $db;
  private $response;
  private $user;
  
  public function __construct()
  {
    parent::__construct();
    $this->db = parent::getDB();
    $this->setResponse(parent::getResponse());
    $this->setStatus('1');
    $this->setMessage('Not initialized.');
    $this->setUser(null);
  }
  
  public function setStatus($status){
    $this->status = $status;
  }
  public function getStatus(){
    return $this->status;
  }
  
  public function setMessage($message){
    $this->message = $message;
  }
  public function getMessage(){
    return $this->message;
  }
  
  public function setUser($user){
    $this->user = $user;
  }
  public function getUser(){
    return $this->user;
  }
  
  public function process($method, $data)
  {
    switch($method)
    {
      case HTTP_METHOD_GET:
      {
        $this->login($data['username'], $data['password']);
        $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getMessage(), 'user'=>$this->getUser()));
      }
      break;
    
      default:
      {}
      break;
    }
  }
  
  private function login($username, $password)
  {
    $trainerObj = null;
    if(!$username || !$password)
    {
      $this->setUser($trainerObj);
      $this->setStatus('1');
      $this->setMessage('Wrong Username or Password!');
    }
    
    $userData = $this->db->getRow("
      SELECT 
          trainer_id,username,password,access_level,active,profile_id,is_clinic,email,is_login,expire_date,lang
      FROM 
          trainer 
      WHERE 
          username = '".mysql_real_escape_string($username)."' AND password = '".mysql_real_escape_string($password)."'
    ");

    if(!empty($userData))
    {
      $trainer_id = $userData['trainer_id'];

      $trainerObj = Trainer::createTrainerById($trainer_id, $this->getDB());
      $this->setUser($trainerObj);
      
      if($userData['active']==0)
      {
          if(strtotime($userData['expire_date'])-time()<=0)
          {
            $this->setStatus('1');
            $this->setMessage('Your account has been expired!');
            return;
          }
          else
          {
            //if account banned
            $this->setStatus('1');
            $this->setMessage('Username was banned for a reason. Please contact support for more details!');
            return;
          }
      }
      elseif($userData['active']==1)
      {
          $set_trial_time = date('Y-m-d H:i:s',strtotime('+14days'));
          $this->db->query("
                              UPDATE 
                                  trainer 
                              SET 
                                  active=2, 
                                  expire_date='".$set_trial_time."',
                                  ip='".$_SERVER['REMOTE_ADDR']."'  
                              WHERE 
                                  trainer_id = ".$userData['trainer_id']." 
                          ");
          
          //if account just registered
          if($userData['is_clinic'] == 2)
          {
            $this->setStatus('0');
            $this->setMessage('Please fill this field.');
          }
          else
          {
            $this->setStatus('0');
            $this->setMessage('You logged in succesfully!');
          }
          return;
      }
      elseif($userData['active']==2)
      {
          //if account trial or payed
          if($userData['is_clinic'] == 2)
          {
              //if account choose neither clinic nor single user
              $this->setStatus('1');
              $this->setMessage('Please fill this field.');
              return;
          }
          
          if(!$userData['email'])
          {
              //if account has no email redirect to edit email page
              $this->setStatus('1');
              $this->setMessage('You have not email address, please fill this field.');
              return;
          }
              
          $this->setStatus('0');
          $this->setMessage('You logged in succesfully!');
          return;
      }
      else
      {
        $this->setStatus('1');
        $this->setMessage('Username or password invalid.');
        return;
      }
    }
    $this->setStatus('1');
    $this->setMessage('Username or password invalid.');
    return;
  }
  
}

?>