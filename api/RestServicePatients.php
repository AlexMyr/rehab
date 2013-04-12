<?php

class Patient {
  public $patient_id;
  public $patient_name;
  public $patient_surname;
  
  public $patient_title;
  public $patient_email;
  public $patient_image_type;
  
  public $patient_records;
  private $dbCon;
  private $owner_id;
  
  public function __construct()
  {
    $this->setId(0);
    $this->setName('');
    $this->setSurname('');
    $this->setTitle('');
    $this->setEmail('');
    $this->setImageType('');
    $this->setRecords(array());
    $this->setCon(null);
    $this->setOwnerId(0);
  }
  
  public function setId($patient_id)
  {
    $this->patient_id = $patient_id;
  }
  
  public function getId()
  {
    return $this->patient_id;
  }
  
  public function setName($patient_name)
  {
    $this->patient_name = $patient_name;
  }
  
  public function getName()
  {
    return $this->patient_name;
  }
  
  public function setSurname($patient_surname)
  {
    $this->patient_surname = $patient_surname;
  }
  
  public function getSurname()
  {
    return $this->patient_surname;
  }
  
  public function setRecords($patient_records)
  {
    $this->patient_records = $patient_records;
  }
  
  public function getRecords()
  {
    return $this->patient_records;
  }
  
  //------------------
  public function setTitle($patient_title)
  {
    $this->patient_title = $patient_title;
  }
  
  public function getTitle()
  {
    return $this->patient_title;
  }
  
  public function setEmail($patient_email)
  {
    $this->patient_email = $patient_email;
  }
  
  public function getEmail()
  {
    return $this->patient_email;
  }
  
  public function setImageType($patient_image_type)
  {
    $this->patient_image_type = $patient_image_type;
  }
  
  public function getImageType()
  {
    return $this->patient_image_type;
  }
  
  public function setOwnerId($owner_id)
  {
    $this->owner_id = $owner_id;
  }
  
  public function getOwnerId()
  {
    return $this->owner_id;
  }
  
  //------------------
  
  public function setCon($dbCon)
  {
    $this->dbCon = $dbCon;
  }
  
  public function getCon()
  {
    return $this->dbCon;
  }
  
  static function createPatient($patient_id, $patient_name, $patient_surname, $patient_title, $patient_email, $patient_image_type, $dbCon, $owner_id)
  {
    $instance = new self();
    $instance->setId($patient_id);
    $instance->setName($patient_name);
    $instance->setSurname($patient_surname);
    $instance->setTitle($patient_title);
    $instance->setEmail($patient_email);
    $instance->setImageType($patient_image_type);
    $instance->setCon($dbCon);
    $instance->setRecords($instance->fillPatientRecords($patient_id));
    $instance->setOwnerId($owner_id);

    return $instance;
  }
  
  static function createNewPatient($patient_name, $patient_surname, $patient_title, $patient_email, $patient_image_type, $owner_id, $dbCon)
  {
    $instance = new self();
    $instance->setId(0);
    $instance->setName($patient_name);
    $instance->setSurname($patient_surname);
    $instance->setTitle($patient_title);
    $instance->setEmail($patient_email);
    $instance->setImageType($patient_image_type);
    $instance->setCon($dbCon);
    $instance->setRecords(array());
    $instance->setOwnerId($owner_id);
    return $instance;
  }
  
  public function insertToDatabase()
  {
    $query = "insert into client (first_name, surname, client_note, email, create_date, modify_date, print_image_type, trainer_id, appeal)
              values('".mysql_real_escape_string($this->getName())."', '".mysql_real_escape_string($this->getSurname())."', '', '".mysql_real_escape_string($this->getEmail())."', NOW(), NOW(), '".mysql_real_escape_string($this->getImageType())."', '".mysql_real_escape_string($this->getOwnerId())."', '".mysql_real_escape_string($this->getTitle())."')";

    $this->dbCon->query($query);
    $this->setId($this->dbCon->insertID());
  }
  
  public function updatePatient()
  {
    if(!$this->checkPatientOwner())
      return false;
    
    $query = "update client set
              first_name='".mysql_real_escape_string($this->getName())."',
              surname='".mysql_real_escape_string($this->getSurname())."',
              email='".mysql_real_escape_string($this->getEmail())."',
              modify_date=NOW(),
              print_image_type='".mysql_real_escape_string($this->getImageType())."',
              appeal='".mysql_real_escape_string($this->getTitle())."'
              where client_id='".mysql_real_escape_string($this->getId())."'
              and trainer_id='".mysql_real_escape_string($this->getOwnerId())."'
              ";

    $this->dbCon->query($query);
    return true;
  }
  
  private function checkPatientOwner()
  {
    $query = "select count(*) from client where client_id='".mysql_real_escape_string($this->getId())."'
              and trainer_id='".mysql_real_escape_string($this->getOwnerId())."'";
    if($this->dbCon->getVar($query))
      return true;
    return false;
  }
  
  private function fillPatientRecords($patient_id)
  {
    $result = array();
    $tmpRecord = null;
    $tmp = $this->dbCon->getResults("select exercise_plan_id, date_created, date_modified, exercise_desc, exercise_program_id from exercise_plan where 1=1 AND exercise_plan.client_id=".mysql_real_escape_string($patient_id)." ");
    foreach($tmp as $row)
    {
      $result[] = PatientRecord::createPatientRecord(
        $row['exercise_plan_id'], $row['date_created'], $row['date_modified'], $row['exercise_desc'], $row['exercise_program_id'], $this->getCon()
      );
    }
    return $result;
  }
  
  static function deleteById($patient_id, $dbCon)
  {
    if($dbCon->getVar("select count(*) from client where client_id=$patient_id"))
    {
      $dbCon->query("delete from client where client_id=$patient_id");
      return true;
    }
    return false;
  }
  
}

class PatientRecord {
  public $record_id;
  public $date_created;
  public $date_modified;
  public $description;
  public $record_exercises;
  private $dbCon;
  
  public function __construct()
  {
    $this->setId(0);
    $this->setDateCreated(0);
    $this->setDateModified(0);
    $this->setDescription('');
    $this->setRecordExercises(array());
    $this->setCon(null);
  }
  
  public function setId($record_id)
  {
    $this->record_id = $record_id;
  }
  
  public function getId()
  {
    return $this->record_id;
  }
  
  public function setDateCreated($date_created)
  {
    $this->date_created = $date_created;
  }
  
  public function getDateCreated()
  {
    return $this->date_created;
  }
  
  public function setDateModified($date_modified)
  {
    $this->date_modified = $date_modified;
  }
  
  public function getDateModified()
  {
    return $this->date_modified;
  }
  
  public function setDescription($description)
  {
    $this->description = $description;
  }
  
  public function getDescription()
  {
    return $this->description;
  }
  
  public function setRecordExercises($record_exercises)
  {
    $this->record_exercises = $record_exercises;
  }
  
  public function getRecordExercises()
  {
    return $this->record_exercises;
  }
  
  public function setCon($dbCon)
  {
    $this->dbCon = $dbCon;
  }
  
  public function getCon()
  {
    return $this->dbCon;
  }
  
  static function createPatientRecord($record_id, $date_created, $date_modified, $description, $record_exercises, $dbCon)
  {
    $instance = new self();
    $instance->setId($record_id);
    $instance->setDateCreated($date_created);
    $instance->setDateModified($date_modified);
    $instance->setDescription($description);
    $instance->setCon($dbCon);
    $instance->setRecordExercises($instance->prepareRecordExercises($record_id, $record_exercises, $dbCon));
    return $instance;
  }
  
  private function prepareRecordExercises($record_id, $record_exercises, $dbCon)
  {
    $tmp = array();
    $record_exercises = explode(',', $record_exercises);
    foreach($record_exercises as $exercise)
    {
      if(!$exercise) continue;
      //$tmpExercise = new Exercise($exercise);

      $tmpExercise = Exercise::getExerciseFromDb($record_id, $exercise, $dbCon);
      $tmp[] = $tmpExercise;
    }
    return $tmp;
  }
}

class Exercise
{
  public $exercise_set_id;
  public $exercise_id;
  public $sets;
  public $repetitions;
  public $time;
  public $both_sides;
  
  public function __construct()
  {
    $this->setExerciseSetId('0');
    $this->setId('0');
    $this->setSets('');
    $this->setRepetitions('');
    $this->setTime('');
    $this->setBothSides('0');
  }
  
  public static function getExerciseFromDb($exercise_plan_id, $exercise_id, $dbCon)
  {
    $result = array();
    $query = "select * from exercise_plan_set where exercise_plan_id='".mysql_real_escape_string($exercise_plan_id)."' and exercise_program_id='".mysql_real_escape_string($exercise_id)."'";
    if($result = $dbCon->getRow($query))
    {
      $instance = new self();
      $instance->setExerciseSetId($result['exercise_set_id']);
      $instance->setId($exercise_id);
      $instance->setSets($result['plan_set_no']);
      $instance->setRepetitions($result['plan_repetitions']);
      $instance->setTime($result['plan_time']);
      $instance->setBothSides($result['both_sides']);
      return $instance;
    }
    else
    {
      $instance = new Exercise();
      $instance->setId($exercise_id);
      return $instance;
    }
  }
  
  public function setId($exercise_id){
    $this->exercise_id = $exercise_id;
  }
  public function getId(){
    return $this->exercise_id;
  }
  
  public function setSets($sets){
    $this->sets = $sets;
  }
  public function getSets(){
    return $this->sets;
  }
  
  public function setRepetitions($repetitions){
    $this->repetitions = $repetitions;
  }
  public function getRepetitions(){
    return $this->repetitions;
  }
  
  public function setTime($time){
    $this->time = $time;
  }
  public function getTime(){
    return $this->time;
  }
  
  public function setBothSides($both_sides){
    $this->both_sides = $both_sides;
  }
  public function getBothSides(){
    return $this->both_sides;
  }
  
  public function setExerciseSetId($exercise_set_id){
    $this->exercise_set_id = $exercise_set_id;
  }
  public function getExerciseSetId(){
    return $this->exercise_set_id;
  }
}

class RestServicePatients extends RestServiceBase {
  private $status;
  private $patients;
  private $db;
  private $response;
  private $error;
  private $patient_id;
  
  public function __construct()
  {
    parent::__construct();
    $this->db = parent::getDB();
    $this->response = parent::getResponse();
    $this->status	= '1';
    $this->patients	= array();
    $this->error = '';
    $this->patient_id = 0;
  }
  
  public function setStatus($status)
  {
    $this->status = $status;
  }
  
  public function getStatus()
  {
    return $this->status;
  }
  
  public function setPatients($patients)
  {
    $this->patients = $patients;
  }
  
  public function getPatients()
  {
    return $this->patients;
  }
  
  public function setError($error)
  {
    $this->error = $error;
  }
  
  public function getError()
  {
    return $this->error;
  }
  
  public function setPatientId($patient_id)
  {
    $this->patient_id = $patient_id;
  }
  
  public function getPatientId()
  {
    return $this->patient_id;
  }
  
  public function process($method, $data)
  {
    switch($method)
    {
      case HTTP_METHOD_GET:
      {
        $this->getPatientsFromDB($data['username']);
        $this->setResponse(array('status'=>$this->getStatus(), 'patients'=>$this->getPatients()));
      }
      break;

      case HTTP_METHOD_POST:
      {
        $this->savePatientToDB($data);
        $this->setResponse(array('status'=>$this->getStatus(), 'patient_id'=>$this->getPatientId(), 'error'=>$this->getError()));
      }
      break;

      case HTTP_METHOD_DELETE:
      {
        $this->deletePatient($data['patient_id']);
        $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError()));
      }
      break;
    
      case HTTP_METHOD_PUT:
      {
        $this->updatePatient($data);
        $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError()));
      }
      break;
      
      
      default:
      {}
      break;
    }
  }
  
  private function getPatientsFromDB($username)
  {
    $resultPatients = array();
    if(!$username)
    {
      $this->setStatus('1');
      $this->setPatients(array());
    }
    
    $patientData = $this->getPatientsByOwnerUsername($username);
    $owner_id = $this->getPatientOwnerId($username);
    if(!empty($patientData))
    {
      foreach($patientData as $patient)
      {
        $resultPatients[] = Patient::createPatient($patient['client_id'], $patient['first_name'], $patient['surname'], $patient['appeal'], $patient['email'], $patient['print_image_type'], $this->getDB(), $owner_id);
      }
      $this->setStatus('0');
      $this->setPatients($resultPatients);
      return;
    }
    else
    {
      $this->setStatus('1');
      $this->setPatients(array());
      return;
    }
    
  }
  
  private function getPatientOwnerId($owner_username)
  {
    $owner_id = $this->db->getVar("select trainer_id from trainer where username='".mysql_real_escape_string($owner_username)."'");
    return $owner_id;
  }
  
  private function getPatientsByOwnerUsername($owner_username)
  {
    $patints = $this->db->getResults("
      select client_id, first_name, surname, email, appeal, print_image_type from client where trainer_id = (select distinct trainer_id from trainer where username = '".mysql_real_escape_string($owner_username)."')
    ");
    return $patints;
  }
  
  private function savePatientToDB($patient_info)
  {
    $owner_id = $this->getPatientOwnerId($patient_info['owner_username']);
    $newPateint = Patient::createNewPatient($patient_info['first_name'], $patient_info['surname'], $patient_info['title'],
                                            $patient_info['email'], $patient_info['image_type'], $owner_id, $this->getDB());
    $newPateint->insertToDatabase();
    if($newPateint->getId())
    {
      $this->setStatus('0');
      $this->setPatientId($newPateint->getId());
      $this->setError('');
    }
    else
    {
      $this->setStatus('1');
      $this->setPatientId($newPateint->getId());
      $this->setError('Error save patient.');
    }
  }
  
  private function deletePatient($patient_id)
  {
    if(Patient::deleteById($patient_id, $this->getDB()))
    {
      $this->setStatus('0');
      $this->setError('');
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Can not delete patient. Patient not exists.');
    }
  }
  
  private function updatePatient($patient_info)
  {
    if($patient_info['patient_id'] && $patient_info['owner_username'])
    {
      $owner_id = $this->getPatientOwnerId($patient_info['owner_username']);
      $updatedPatient = Patient::createPatient($patient_info['patient_id'], $patient_info['first_name'], $patient_info['surname'], $patient_info['title'],
                                            $patient_info['email'], $patient_info['image_type'], $this->getDB(), $owner_id);
      if($updatedPatient->updatePatient())
      {
        $this->setStatus('0');
        $this->setError('');
      }
      else
      {
        $this->setStatus('1');
        $this->setError('Can not update patient.');
      }
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Can not update patient.');
    }
  }
  
}

?>