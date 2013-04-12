<?php

class Category {
  public $category_id;
  public $category_name;
  public $subcategories;
  private $dbCon;
  
  public function __construct()
  {
    $this->setId(0);
    $this->setName('');
    $this->setSubcategories(array());
    $this->setCon(null);
  }
  
  static function createCategory($category_id, $category_name, $dbCon)
  {
    $instance = new self();
    $instance->setId($category_id);
    $instance->setName($category_name);
    $instance->setCon($dbCon);
    $instance->setSubcategories($instance->fillSubcategories($category_id));
    return $instance;
  }
  
  private function fillSubcategories($category_id)
  {
    $result = array();
    $tmp = $this->dbCon->getResults("select category_id, category_name from programs_category where category_level>0 and category_id in(select category_id from  programs_category_subcategory where parent_id=".mysql_real_escape_string($category_id).") ");

    foreach($tmp as $subcat)
    {
      $result[] = Subcategory::createSubcategory($subcat['category_id'], $subcat['category_name'], 0);
    }
    return $result;
  }
  
  public function setId($category_id){
    $this->category_id = $category_id;
  }
  public function getId(){
    return $this->category_id;
  }
  
  public function setName($category_name){
    $this->category_name = $category_name;
  }
  public function getName(){
    return $this->category_name;
  }
  
  public function setSubcategories($subcategories){
    $this->subcategories = $subcategories;
  }
  public function getSubcategories(){
    return $this->subcategories;
  }
  
  public function setCon($dbCon){
    $this->dbCon = $dbCon;
  }
  public function getCon(){
    return $this->dbCon;
  }
}

class Subcategory {
  public $subcategory_id;
  public $subcategory_name;
  protected $category_id;
  
  public function __construct()
  {
    $this->setSubcategoryId(0);
    $this->setSubcategoryName('');
    $this->setCategoryId(0);
  }
  
  static function createSubcategory($subcategory_id, $subcategory_name, $category_id)
  {
    $instance = new self();
    $instance->setSubcategoryId($subcategory_id);
    $instance->setSubcategoryName($subcategory_name);
    $instance->setCategoryId($category_id);
    return $instance;
  }
  
  public function setSubcategoryId($subcategory_id){
    $this->subcategory_id = $subcategory_id;
  }
  public function getSubcategoryId(){
    return $this->subcategory_id;
  }
  
  public function setSubcategoryName($subcategory_name){
    $this->subcategory_name = $subcategory_name;
  }
  public function getSubcategoryName(){
    return $this->subcategory_name;
  }
  
  public function setCategoryId($category_id){
    $this->category_id = $category_id;
  }
  public function getCategoryId(){
    return $this->category_id;
  }
}

class SubcategoryExt {
  public $subcategory_id;
  public $subcategory_name;
  public $category_id;
  
  public function __construct()
  {
    $this->setSubcategoryId(0);
    $this->setSubcategoryName('');
    $this->setCategoryId(0);
  }
  
  static function createSubcategory($subcategory_id, $subcategory_name, $category_id)
  {
    $instance = new self();
    $instance->setSubcategoryId($subcategory_id);
    $instance->setSubcategoryName($subcategory_name);
    $instance->setCategoryId($category_id);
    return $instance;
  }
  
  public function setSubcategoryId($subcategory_id){
    $this->subcategory_id = $subcategory_id;
  }
  public function getSubcategoryId(){
    return $this->subcategory_id;
  }
  
  public function setSubcategoryName($subcategory_name){
    $this->subcategory_name = $subcategory_name;
  }
  public function getSubcategoryName(){
    return $this->subcategory_name;
  }
  
  public function setCategoryId($category_id){
    $this->category_id = $category_id;
  }
  public function getCategoryId(){
    return $this->category_id;
  }
}

class ExerciseProgram {
  public $exercise_id;
  public $subcategory_id;
  public $exercise_name;
  public $exercise_info;
  public $exercise_image_s;
  public $exercise_image_l;
  public $exercise_lineart_s;
  public $exercise_lineart_l;

  public function __construct()
  {
    $this->setId(0);
    $this->setSubcat(array());
    $this->setName('');
    $this->setInfo('');
    $this->setImageSmall('');
    $this->setImageLarge('');
    $this->setLineartSmall('');
    $this->setLineartLarge('');
  }
  
  static function createExercise(
    $exercise_id, $subcategory_id, $exercise_name, $exercise_info, $exercise_image_s, $exercise_image_l, $exercise_lineart_s, $exercise_lineart_l
  )
  {
    $instance = new self();
    $instance->setId($exercise_id);
    $instance->setSubcat($subcategory_id);
    $instance->setName($exercise_name);
    $instance->setInfo($exercise_info);
    $instance->setImageSmall($exercise_image_s);
    $instance->setImageLarge($exercise_image_l);
    $instance->setLineartSmall($exercise_lineart_s);
    $instance->setLineartLarge($exercise_lineart_l);
    return $instance;
  }
  
  public function setId($exercise_id){
    $this->exercise_id = $exercise_id;
  }
  public function getId(){
    return $this->exercise_id;
  }
  
  public function setSubcat($subcategory_id){
    $this->subcategory_id = $subcategory_id;
  }
  public function getSubcat(){
    return $this->subcategory_id;
  }
  
  public function setName($exercise_name){
    $this->exercise_name = $exercise_name;
  }
  public function getName(){
    return $this->exercise_name;
  }
  
  public function setInfo($exercise_info){
    $this->exercise_info = $exercise_info;
  }
  public function getInfo(){
    return $this->exercise_info;
  }
  
  public function setImageSmall($exercise_image_s){
    $this->exercise_image_s = $exercise_image_s;
  }
  public function getImageSmall(){
    return $this->exercise_image_s;
  }
  
  public function setImageLarge($exercise_image_l){
    $this->exercise_image_l = $exercise_image_l;
  }
  public function getImageLarge(){
    return $this->exercise_image_l;
  }
  
  public function setLineartSmall($exercise_lineart_s){
    $this->exercise_lineart_s = $exercise_lineart_s;
  }
  public function getLineartSmall(){
    return $this->exercise_lineart_s;
  }
  
  public function setLineartLarge($exercise_lineart_l){
    $this->exercise_lineart_l = $exercise_lineart_l;
  }
  public function getLineartLarge(){
    return $this->exercise_lineart_l;
  }
  
}

class ExercisePlan {
  public $record_id;
  public $exercise_id;
  public $exercise_desc;
  public $owner_id;
  public $patient_id;
  private $date_created;
  private $date_modified;
  
  public function setId($record_id){
    $this->record_id = $record_id;
  }
  public function getId(){
    return $this->record_id;
  }
  
  public function setProgramId($exercise_id){
    $this->exercise_id = $exercise_id;
  }
  public function getProgramId(){
    return $this->exercise_id;
  }
  
  public function setDescription($exercise_desc){
    $this->exercise_desc = $exercise_desc;
  }
  public function getDescription(){
    return $this->exercise_desc;
  }
  
  public function setOwner($owner_id){
    $this->owner_id = $owner_id;
  }
  public function getOwner(){
    return $this->owner_id;
  }
  
  public function setPatient($patient_id){
    $this->patient_id = $patient_id;
  }
  public function getPatient(){
    return $this->patient_id;
  }
  
  public function setDateCreated($date_created){
    $this->date_created = $date_created;
  }
  public function getDateCreated(){
    return $this->date_created;
  }
  
  public function setDateModified($date_modified){
    $this->date_modified = $date_modified;
  }
  public function getDateModified(){
    return $this->date_modified;
  }
  
  public function __construct()
  {
    $this->setId(0);
    $this->setProgramId(array());
    $this->setDescription('');
    $this->setOwner(0);
    $this->setPatient(0);
    $this->setDateCreated('0');
    $this->setDateModified('0');
  }
  
  static function createExercisePlan(
    $record_id, $exercise_id, $exercise_desc, $owner_id, $patient_id, $date_created, $date_modified
  )
  {
    $instance = new self();
    $instance->setId($record_id);
    $instance->setProgramId($exercise_id);
    $instance->setDescription($exercise_desc);
    $instance->setOwner($owner_id);
    $instance->setPatient($patient_id);
    $instance->setDateCreated($date_created);
    $instance->setDateModified($date_modified);
    return $instance;
  }
  
  public function insertToDatabase($dbCon)
  {
    $query = "insert into exercise_plan (exercise_program_id, date_created, date_modified, trainer_id, client_id, exercise_desc)
              values('".mysql_real_escape_string(implode(',', $this->getProgramId()))."', ".mysql_real_escape_string($this->getDateCreated()).", ".mysql_real_escape_string($this->getDateModified()).", '".mysql_real_escape_string($this->getOwner())."', '".mysql_real_escape_string($this->getPatient())."', '".mysql_real_escape_string($this->getDescription())."')";
    $dbCon->query($query);
    $this->setId($dbCon->insertID());
  }
  
  static function deleteById($record_id, $dbCon)
  {
    if($dbCon->getVar("select count(*) from exercise_plan where exercise_plan_id=$record_id"))
    {
      $dbCon->query("delete from exercise_plan where exercise_plan_id=$record_id");
      return true;
    }
    return false;
  }
  
  public function updateExercisePlan($dbCon)
  {
    if(!$this->checkExercisePlanOwner($dbCon))
      return false;
    
    $query = "update exercise_plan set
              exercise_program_id='".mysql_real_escape_string(implode(',', $this->getProgramId()))."',
              date_created=".mysql_real_escape_string($this->getDateCreated()).",
              date_modified=".mysql_real_escape_string($this->getDateModified()).",
              exercise_desc='".mysql_real_escape_string($this->getDescription())."'
              where exercise_plan_id='".mysql_real_escape_string($this->getId())."'
              and client_id='".mysql_real_escape_string($this->getPatient())."'
              ";

    $dbCon->query($query);
    return true;
  }
  
  private function checkExercisePlanOwner($dbCon)
  {
    $query = "select count(*) from exercise_plan where exercise_plan_id='".mysql_real_escape_string($this->getId())."'
              and client_id='".mysql_real_escape_string($this->getPatient())."'";
    if($dbCon->getVar($query))
      return true;
    return false;
  }
  
}

class ExercisePlanSet {
  public $exercise_set_id;
  public $record_id;
  public $exercise_id;
  public $description;
  public $sets;
  public $repetitions;
  public $time;
  private $is_program_plan;
  public $both_sides;
  public $patient_id;
  public $owner_id;
  
  public function __construct()
  {
    $this->setId(0);
    $this->setPlanId(0);
    $this->setProgramId(0);
    $this->setPlanDescription('');
    $this->setPlanSetNo('');
    $this->setPlanRepetitions('');
    $this->setPlanTime('');
    $this->setIsProgramPlan(0);
    $this->setBothSides(0);
    $this->setPatient(0);
    $this->setOwner(0);
  }
  
  static function createExercisePlanSet(
    $exercise_set_id, $record_id, $exercise_id, /*$description,*/ $sets,
    $repetitions, $time, $is_program_plan, $both_sides, $patient_id, $owner_id
  )
  {
    $instance = new self();
    $instance->setId($exercise_set_id);
    $instance->setPlanId($record_id);
    $instance->setProgramId($exercise_id);
    //$instance->setPlanDescription($description);
    $instance->setPlanSetNo($sets);
    $instance->setPlanRepetitions($repetitions);
    $instance->setPlanTime($time);
    $instance->setIsProgramPlan($is_program_plan);
    $instance->setBothSides($both_sides);
    $instance->setPatient($patient_id);
    $instance->setOwner($owner_id);
    
    return $instance;
  }
  
  public function getDescription($dbCon)
  {
    $query = "select description from programs_translate_en where programs_id=".$this->getProgramId();
    $this->setPlanDescription($dbCon->getVar($query));
  }
  
  public function addExercisePlanSet($dbCon)
  {
    $query = "insert into exercise_plan_set (exercise_plan_id, exercise_program_id, plan_description, plan_set_no, plan_repetitions,
              plan_time, trainer_id, client_id, is_program_plan, both_sides)
              values('".mysql_real_escape_string($this->getPlanId())."', '".mysql_real_escape_string($this->getProgramId())."',
              '".mysql_real_escape_string($this->getPlanDescription())."', '".mysql_real_escape_string($this->getPlanSetNo())."',
              ".mysql_real_escape_string($this->getPlanRepetitions()).", '".mysql_real_escape_string($this->getPlanTime())."',
              ".mysql_real_escape_string($this->getOwner()).", '".mysql_real_escape_string($this->getPatient())."',
              '".mysql_real_escape_string($this->getIsProgramPlan())."', '".mysql_real_escape_string($this->getBothSides())."')";

    $dbCon->query($query);
    $this->setId($dbCon->insertID());
  }
  
  public function updateExercisePlanSet($dbCon)
  {
    if(!$this->checkExercisePlanSetOwner($dbCon))
      return false;
    
    $query = "update exercise_plan_set set
              exercise_plan_id='".mysql_real_escape_string($this->getPlanId())."',
              exercise_program_id='".mysql_real_escape_string($this->getProgramId())."',
              plan_description='".mysql_real_escape_string($this->getPlanDescription())."',
              plan_set_no='".mysql_real_escape_string($this->getPlanSetNo())."',
              plan_repetitions='".mysql_real_escape_string($this->getPlanRepetitions())."',
              plan_time='".mysql_real_escape_string($this->getPlanTime())."',
              is_program_plan='".mysql_real_escape_string($this->getIsProgramPlan())."',
              both_sides='".mysql_real_escape_string($this->getBothSides())."'
              where exercise_set_id='".mysql_real_escape_string($this->getId())."'
              and client_id='".mysql_real_escape_string($this->getPatient())."'
              ";

    $dbCon->query($query);
    return true;
  }
  
  static function deleteById($exercise_set_id, $dbCon)
  {
    if($dbCon->getVar("select count(*) from exercise_plan_set where exercise_set_id=$exercise_set_id"))
    {
      $dbCon->query("delete from exercise_plan_set where exercise_set_id=$exercise_set_id");
      return true;
    }
    return false;
  }
  
  private function checkExercisePlanSetOwner($dbCon)
  {
    $query = "select count(*) from exercise_plan_set where exercise_set_id='".mysql_real_escape_string($this->getId())."'
              and client_id='".mysql_real_escape_string($this->getPatient())."'";

    if($dbCon->getVar($query))
      return true;
    return false;
  }
  
  public function setId($exercise_set_id){
    $this->exercise_set_id = $exercise_set_id;
  }
  public function getId(){
    return $this->exercise_set_id;
  }
  
  public function setPlanId($record_id){
    $this->record_id = $record_id;
  }
  public function getPlanId(){
    return $this->record_id;
  }
  
  public function setProgramId($exercise_id){
    $this->exercise_id = $exercise_id;
  }
  public function getProgramId(){
    return $this->exercise_id;
  }
  
  public function setPlanDescription($description){
    $this->description = $description;
  }
  public function getPlanDescription(){
    return $this->description;
  }
  
  public function setPlanSetNo($sets){
    $this->sets = $sets;
  }
  public function getPlanSetNo(){
    return $this->sets;
  }
  
  public function setPlanRepetitions($repetitions){
    $this->repetitions = $repetitions;
  }
  public function getPlanRepetitions(){
    return $this->repetitions;
  }
  
  public function setPlanTime($time){
    $this->time = $time;
  }
  public function getPlanTime(){
    return $this->time;
  }
  
  public function setIsProgramPlan($is_program_plan){
    $this->is_program_plan = $is_program_plan;
  }
  public function getIsProgramPlan(){
    return $this->is_program_plan;
  }
  
  public function setBothSides($both_sides){
    $this->both_sides = $both_sides;
  }
  public function getBothSides(){
    return $this->both_sides;
  }
  
  public function setOwner($owner_id){
    $this->owner_id = $owner_id;
  }
  public function getOwner(){
    return $this->owner_id;
  }
  
  public function setPatient($patient_id){
    $this->patient_id = $patient_id;
  }
  public function getPatient(){
    return $this->patient_id;
  }
  
}

class RestServiceExercises extends RestServiceBase {
  private $status;
  private $record_id;
  private $categories;
  private $subcategories;
  private $exercises;
  private $db;
  private $response;
  private $error;
  private $exercise_set_id;
  
  public function __construct()
  {
    parent::__construct();
    $this->db = parent::getDB();
    $this->setResponse(parent::getResponse());
    $this->setStatus('1');
    $this->setCategories(array());
    $this->setSubcategories(array());
    $this->setExercises(array());
    $this->setExercisePlanId('0');
    $this->setError('');
    $this->setExerciseSetId('0');
  }
  
  public function setStatus($status){
    $this->status = $status;
  }
  public function getStatus(){
    return $this->status;
  }
  
  public function setExercisePlanId($record_id){
    $this->record_id = $record_id;
  }
  public function getExercisePlanId(){
    return $this->record_id;
  }
  
  public function setExerciseSetId($exercise_set_id){
    $this->exercise_set_id = $exercise_set_id;
  }
  public function getExerciseSetId(){
    return $this->exercise_set_id;
  }
  
  public function setCategories($categories){
    $this->categories = $categories;
  }
  public function getCategories(){
    return $this->categories;
  }
  
  public function setSubcategories($subcategories){
    $this->subcategories = $subcategories;
  }
  public function getSubcategories(){
    return $this->subcategories;
  }
  
  public function setExercises($exercises){
    $this->exercises = $exercises;
  }
  public function getExercises(){
    return $this->exercises;
  }
  
  public function setError($error){
    $this->error = $error;
  }
  public function getError(){
    return $this->error;
  }
  
  public function process($method, $data)
  {
    switch($method)
    {
      case HTTP_METHOD_GET:
      {
        if(isset($data['cat_id']))
          $this->getExercisesFromDB($data['cat_id']);
        else
          $this->getExercisesFromDB();
        
        $this->setResponse(
          array(
            'status'=>$this->getStatus(),
            'categories'=>$this->getCategories(),
            'subcategories'=>$this->getSubcategories(),
            'exercises'=>$this->getExercises()
          )
        );
      }
      break;
    
      case HTTP_METHOD_POST:
      {
        if(isset($data['plan_set']) && $data['plan_set'])
        {
          $this->addExercisePlanSet($data);
          $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError(), 'exercise_set_id'=>$this->getExerciseSetId()));
        }
        else
        {
          $this->addExerciseToDb($data);
          $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError(), 'record_id'=>$this->getExercisePlanId()));
        }
      }
      break;
    
      case HTTP_METHOD_DELETE:
      {
        if(isset($data['plan_set']) && $data['plan_set'])
        {
          $this->deleteExercisePlanSet($data['exercise_set_id']);
          $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError()));
        }
        else
        {
          $this->deleteExercisePlan($data['record_id']);
          $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError()));
        }
      }
      break;
    
      case HTTP_METHOD_PUT:
      {
        if(isset($data['plan_set']) && $data['plan_set'])
        {
          $this->updateExercisePlanSet($data);
          $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError()));
        }
        else
        {
          $this->updateExercisePlan($data);
          $this->setResponse(array('status'=>$this->getStatus(), 'error'=>$this->getError()));
        }
      }
      break;
    
      default:
      {}
      break;
    }
  }
  
  private function getExercisesFromDB($cat_id = 0)
  {
    $resultCategories = array();
    $resultSubcategories = array();
    $resultExercises = array();
    if(!$last_transfer)
    {
      $this->setStatus('1');
      $this->setCategories(array());
    }
    
    $categoriesData = $this->db->getResults("
      select category_id, category_name from programs_category where category_level=0
    ");
    foreach($categoriesData as $cat)
    {
      $resultCategories[] = Category::createCategory($cat['category_id'], $cat['category_name'], $this->getDB());
    }
    $this->setCategories($resultCategories);
    
    $subcategoriesData = $this->db->getResults("
      select pc.category_id, pc.category_name, pcs.parent_id from programs_category pc left join programs_category_subcategory pcs on pc.category_id=pcs.category_id where category_level>0
    ");
    foreach($subcategoriesData as $subcat)
    {
      $resultSubcategories[] = SubcategoryExt::createSubcategory($subcat['category_id'], $subcat['category_name'], $subcat['parent_id']);
    }
    $this->setSubcategories($resultSubcategories);
    
    $where = '';
    if($cat_id)
    {
      $where = " and pic.category_id=".mysql_real_escape_string($cat_id);
    }

    $exercisesData = $this->db->getResults("
      select distinct p.programs_id, p.lineart, p.thumb_lineart, p.image, p.thumb_image, pte.programs_title, pte.description
      from programs p
      left join programs_translate_en pte on pte.programs_id=p.programs_id
      left join programs_in_category pic on pic.programs_id=p.programs_id
      where 1=1 $where
    ");
    foreach($exercisesData as $exercise)
    {
      $resultExercises[] = ExerciseProgram::createExercise(
        $exercise['programs_id'],
        $this->getExerciseProgramsCategories($exercise['programs_id']),
        $exercise['programs_title'],
        $exercise['description'],
        $this->getImageBase64ByName($exercise['image'], IMAGE_SIZE_SMALL),
        $this->getImageBase64ByName($exercise['image'], IMAGE_SIZE_LARGE),
        $this->getImageBase64ByName($exercise['lineart'], IMAGE_SIZE_SMALL),
        $this->getImageBase64ByName($exercise['lineart'], IMAGE_SIZE_LARGE)
      );
    }

    $this->setExercises($resultExercises);
    $this->setStatus('0');
    return;
  }
  
  private function getExerciseProgramsCategories($programId)
  {
    $categories = $this->db->getResults("select category_id as subcategory_id from  programs_in_category where programs_id=$programId");
    return $categories;
  }
  
  private function getImageBase64ByName($imgName, $size)
  {
    $url = DOMAIN_NAME."/phpthumb/phpThumb.php?src=../upload/{$imgName}&wl={$size}&hp={$size}";
    //$img = file_get_contents($url);
    $img = $url;//base64_encode($url);
    return $img;
  }
  
  private function addExerciseToDb($exercise_info)
  {
    if(!is_array($exercise_info['exercise_id']))
    {
      $exercise_info['exercise_id'] = array($exercise_info['exercise_id']);
    }
    
    if(!empty($exercise_info['exercise_id']) && $exercise_info['exercise_id'][0] && $exercise_info['patient_id'])
    {
      $owner_id = $this->getOwnerByPatientId($exercise_info['patient_id']);
      
      $exercise_plan = ExercisePlan::createExercisePlan(0, $exercise_info['exercise_id'], $exercise_info['exercise_desc'], $owner_id, $exercise_info['patient_id'], 'NOW()', 'NOW()');
      $exercise_plan->insertToDatabase($this->getDB());
      if($exercise_plan->getId())
      {
        $this->setStatus('0');
        $this->setError('');
        $this->setExercisePlanId($exercise_plan->getId());
      }
      else
      {
        $this->setStatus('1');
        $this->setError('Error save exercise.');
      }
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Error save exercise.');
    }
    
  }
  
  private function getOwnerByPatientId($patinet_id)
  {
    return $this->db->getVar("select trainer_id from client where client_id=".mysql_real_escape_string($patinet_id));
  }
  
  private function deleteExercisePlan($record_id)
  {
    if(ExercisePlan::deleteById($record_id, $this->getDB()))
    {
      $this->setStatus('0');
      $this->setError('');
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Can not delete exercise plan. Exercise plan not exists.');
    }
  }
  
  private function updateExercisePlan($exercise_plan_info)
  {
    if($exercise_plan_info['record_id'] && $exercise_plan_info['patient_id'])
    {
      $owner_id = $this->getOwnerByPatientId($exercise_plan_info['patient_id']);
      
      $updatedExercisePlan = ExercisePlan::createExercisePlan($exercise_plan_info['record_id'], $exercise_plan_info['exercise_id'],
                                                              $exercise_plan_info['exercise_desc'], $owner_id, $exercise_plan_info['patient_id'],
                                                              'NOW()', 'NOW()');
      
      if($updatedExercisePlan->updateExercisePlan($this->getDB()))
      {
        $this->setStatus('0');
        $this->setError('');
      }
      else
      {
        $this->setStatus('1');
        $this->setError('Can not update exercise plan.');
      }
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Can not update exercise plan.');
    }
  }
  
  //addExercisePlanSet
  private function addExercisePlanSet($exercise_info)
  {
    if($exercise_info['record_id'] && $exercise_info['patient_id'])
    {
      $owner_id = $this->getOwnerByPatientId($exercise_info['patient_id']);
      
      $exercise_plan_set = ExercisePlanSet::createExercisePlanSet(0, $exercise_info['record_id'], $exercise_info['exercise_id'], $exercise_info['sets'],
                                                                  $exercise_info['repetitions'], $exercise_info['time'], 0, 
                                                                   $exercise_info['both_sides'], $exercise_info['patient_id'], $owner_id);
      $exercise_plan_set->getDescription($this->getDB());
      $exercise_plan_set->addExercisePlanSet($this->getDB());

      if($exercise_plan_set->getId())
      {
        $this->setStatus('0');
        $this->setError('');
        $this->setExerciseSetId($exercise_plan_set->getId());
      }
      else
      {
        $this->setStatus('1');
        $this->setError('Error save exercise plan set.');
      }
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Error save exercise plan set.');
    }
    
  }
  
  private function updateExercisePlanSet($exercise_info)
  {
    if($exercise_info['exercise_set_id'] && $exercise_info['patient_id'] && $exercise_info['exercise_id'])
    {
      $owner_id = $this->getOwnerByPatientId($exercise_info['patient_id']);

      $exercise_plan_set = ExercisePlanSet::createExercisePlanSet($exercise_info['exercise_set_id'], $exercise_info['record_id'], $exercise_info['exercise_id'], $exercise_info['sets'],
                                                                  $exercise_info['repetitions'], $exercise_info['time'], 0, 
                                                                  $exercise_info['both_sides'], $exercise_info['patient_id'], $owner_id);

      $exercise_plan_set->getDescription($this->getDB());
      if($exercise_plan_set->updateExercisePlanSet($this->getDB()))
      {
        $this->setStatus('0');
        $this->setError('');
      }
      else
      {
        $this->setStatus('1');
        $this->setError('Can not update exercise plan set.');
      }
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Can not update exercise plan set.');
    }
  }
  
  private function deleteExercisePlanSet($exercise_set_id)
  {
    if(ExercisePlanSet::deleteById($exercise_set_id, $this->getDB()))
    {
      $this->setStatus('0');
      $this->setError('');
    }
    else
    {
      $this->setStatus('1');
      $this->setError('Can not delete exercise plan set. Exercise plan set not exists.');
    }
  }
}

?>