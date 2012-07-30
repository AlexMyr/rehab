<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class member
{
  var $dbu;

function member()
{
    $this->dbu=new mysql_db;
}
/****************************************************************
* function add(&$ld)                                            *
****************************************************************/

function add(&$ld)
{
	if(!$this->add_validate($ld))
	{
		return false;
	}
            
   //$date=mktime ( 0, 0, 0, $ld['s_month'], $ld['s_day'], $ld['s_year']); 
   if(!$ld['clinic_name'] || empty($ld['clinic_name'])) $is_clinic = ", is_clinic='0'";
   else $is_clinic = ", is_clinic='1'";
	$ld['trainer_id'] = $this->dbu->query_get_id("
									INSERT INTO 
												trainer 
									SET 
												username='".$ld['username']."', 
												first_name='".$ld['first_name']."',
												surname='".$ld['surname']."',
												email='".$ld['email']."',
												password='".$ld['password']."', 
												create_date=NOW(), 
												is_trial='1', 
												expire_date='', 
												active = '1',
												clinic_name = '".$ld['clinic_name']."' ".$is_clinic."
									");
	
	//$this->dbu->query("INSERT INTO trainer_profile
	//				  SET
	//					company_name = '".$ld['clinic_name']."',
	//					first_name = '".$ld['first_name']."',
	//					surname = '".$ld['surname']."',
	//					address = '".$ld['address']."',
	//					city = '".$ld['city']."',
	//					post_code = '".$ld['post_code']."',
	//					website = '".$ld['website']."',
	//					phone = '".$ld['phone']."',
	//					mobile = '".$ld['mobile']."',
	//					email = '".$ld['email']."',
	//					trainer_id = '".$ld['trainer_id']."'
	//				  ");
	
	$this->dbu->query("
					  INSERT INTO 
								  trainer_header_paper 
					  SET 
								  company_name='".$ld['clinic_name']."', 
								  first_name='".$ld['first_name']."', 
								  surname='".$ld['surname']."', 
								  address='".$ld['address']."', 
								  city='".$ld['city']."', 
								  post_code = '".$ld['post_code']."',
								  website = '".$ld['website']."',
								  phone = '".$ld['phone']."',
								  mobile = '".$ld['mobile']."',
								  email = '".$ld['email']."',
								  trainer_id = '".$ld['trainer_id']."'
					  ");
    
    $ld['error']="Member successfully added.";

    return true;
}

function update(&$ld)
{
	if(!$this->update_validate($ld))
	{
		return false;
	}       
    $this->dbu->query("update trainer set                       
                       surname='".$ld['surname']."',
                       first_name='".$ld['first_name']."',
					   password='".$ld['password']."',
					   username='".$ld['username']."',             
                       email='".$ld['email']."',
					   clinic_name = '".$ld['clinic_name']."'						
                       where trainer_id=".$ld['trainer_id']."");
	
	//$this->dbu->query("update trainer_profile set
	//				  company_name = '".$ld['clinic_name']."',
	//					first_name = '".$ld['first_name']."',
	//					surname = '".$ld['surname']."',
	//					address = '".$ld['address']."',
	//					city = '".$ld['city']."',
	//					post_code = '".$ld['post_code']."',
	//					website = '".$ld['website']."',
	//					phone = '".$ld['phone']."',
	//					mobile = '".$ld['mobile']."',
	//					email = '".$ld['email']."'
	//					where trainer_id = '".$ld['trainer_id']."'
	//				  ");
	
	$this->dbu->query("
								UPDATE 
									trainer_header_paper 
								SET 
									#company_name='".$ld['clinic_name']."',
									address='".$ld['address']."',
									first_name='".$ld['first_name']."',
									surname='".$ld['surname']."',
									post_code='".$ld['post_code']."',
									website='".$ld['website']."',
									phone='".$ld['phone']."',
									mobile='".$ld['mobile']."',
									email='".$ld['email']."',
									city='".$ld['city']."'
								WHERE 
									trainer_id='".$ld['trainer_id']."'");
	
    $ld['error'].="The record was succesfully updated.";
    return true;
    //is_login = '".$ld['is_login']."'
}

function delete(&$ld)
{
	global  $script_path;
	
        if(!$this->delete_validate($ld))
        {
                return false;
        }        
        $this->dbu->query("delete from exercise_plan_set where trainer_id='".$ld['trainer_id']."'");
        $this->dbu->query("delete from exercise_plan where trainer_id='".$ld['trainer_id']."'");
        $this->dbu->query("delete from client where trainer_id='".$ld['trainer_id']."'");
        $this->dbu->query("delete from trainer_header_paper where trainer_id='".$ld['trainer_id']."'");
        $this->dbu->query("delete from trainer_profile where trainer_id='".$ld['trainer_id']."'");
        $this->dbu->query("delete from trainer where trainer_id='".$ld['trainer_id']."'");
        
        $ld['error'].="All records for this trainer successfully deleted.";
        return true;
}        
/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/
  
function add_validate(&$ld)
{
    $is_ok=true;
    //if(!$ld['surname'])
    //{
    //    $ld['error'].="Please fill in ' Surname '."."<br>";
    //    $is_ok=false;
    //}    
    // if(!$ld['first_name'])
    //{
    //    $ld['error'].="Please fill in ' First Name '."."<br>";
    //    $is_ok=false;
    //} 
	    if(!$ld['username'])
    {
        $ld['error'].="Please fill in ' User '."."<br>";
        $is_ok=false;
    } 
    if(!$ld['password'])
    {
        $ld['error'].="Please fill in ' Password '."."<br>";
        $is_ok=false;
    } 
    if(!$ld['confirm_password'])
    {
        $ld['error'].="Please fill in ' Confirm Password '."."<br>";
        $is_ok=false;
    }
	if(strlen($ld['password'])<5 || strlen($ld['confirm_password'])<5)
    {
        $ld['error'].="The password must be over 5 characters long."."<br>";
        $is_ok=false;
    }
    if($ld['confirm_password'] != $ld['password'])
    {
        $ld['error'].="Password doesn't match. Please try again."."<br>";
        $is_ok=false;
    }    
    if(!$ld['email'])
    {
        $ld['error'].="Please fill in ' Email '."."<br>";
        $is_ok=false;
    }
//	
//	if(!$ld['address'])
//    {
//        $ld['error'].="Please fill in ' Address '."."<br>";
//        $is_ok=false;
//    }
//    if(!$ld['city'])
//    {
//        $ld['error'].="Please fill in ' City '."."<br>";
//        $is_ok=false;
//    }
//	if(!$ld['post_code'])
//    {
//        $ld['error'].="Please fill in ' Post Code '."."<br>";
//        $is_ok=false;
//    }
	
	
    elseif (!secure_email($ld['email']))
		{
		    $ld['error'].='Please fill in the \' Email \' field with a valid value<br />';
		    $is_ok=false;
		}
    return $is_ok;
}

function update_validate(&$ld)
{
        $is_ok=true;
        if (!is_numeric($ld['trainer_id']))
        {
            $ld['error'].="This record doesn't exist.<br>";
            return false;
        }
        $this->dbu->query("select trainer_id from trainer where trainer_id='".$ld['trainer_id']."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].="This record doesn't exist or it couldn't be find in the database.<br>";
            return false;
        }
        return $this->add_validate($ld);
}
function delete_validate(&$ld)
{
  $is_ok=true;
  if (!is_numeric($ld['trainer_id']))
  {
	$ld['error'].="This record doesn't exist.<br>";
	return false;
  }
  $this->dbu->query("select trainer_id from trainer where trainer_id='".$ld['trainer_id']."'");
  if(!$this->dbu->move_next())
  {
	$ld['error'].="This record doesn't exist or it couldn't be find in the database.<br>";
	return false;
  }

  return $is_ok;
}

function deactivate(&$ld)
{       
  $this->dbu->query("update trainer set active=0 where trainer_id=".$ld['trainer_id']."");                  
  $ld['error'].="The member was succesfully banned.";
  return true;
}
function activate(&$ld)
{       
  $this->dbu->query("update trainer set active=2 where trainer_id=".$ld['trainer_id']."");                  
  $ld['error'].="The member was succesfully activated.";
  return true;
}
function activate_full_rights(&$ld)
{
  $expire_date = date("Y-m-d H:i:S", (time() + 5*365*24*3600));
  $this->dbu->query("update trainer set active=2,is_trial=0,expire_date='$expire_date' where trainer_id=".$ld['trainer_id']."");                  
  $ld['error'].="The member has full rights.";
  return true;
}
function trial($ld)
{
  $expire_date = date("Y-m-d H:i:S", (time() + 14*24*3600));
  $this->dbu->query("update trainer set active=2,is_trial=1,expire_date='$expire_date' where trainer_id=".$ld['trainer_id']."");                  
  $ld['error'].="The member has full rights.";
  return true;
}
/****************************************************************
* function send_notiffication_mail_activate(&$ld)                        *
****************************************************************/
}//end class
?>

