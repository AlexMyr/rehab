<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
class member
{
	var $dbu;

	function member()
		{
			$this->dbu = new mysql_db();
		}
		
	/****************************************************************
	* function join_now(&$ld)                                       *
	****************************************************************/
	function join_now(&$ld)
	{
		if(!$this->validate_join($ld))
		{
			return false;
		}
			
		global $user_level;
	    $this->dbu->query("
							SELECT 
								trainer_id
							FROM 
								trainer 
							WHERE 
								username = '".$ld['join_email']."'
						");
	    /* CHECK IF EMAIL EXIST IN DB, IF NOT, GENERATE A RANDOM PASSWORD, SAVE IT IN DB AND SEND MAIL TO THAT ADDRESS */
		if($this->dbu->move_next())
			{
				$ld['error'] = 'Username with this email already exist!';
				return false;
			}
		else 
			{ 
			/*	require_once ('misc/PapApi.class.php');
				// login (as merchant)
				
				$session = new Gpf_Api_Session(AFFILIATES_API_M_URL);
				if(!$session->login(AFFILIATES_API_M_USERNAME, AFFILIATES_API_M_PASSWORD))
					{
						die("Cannot login. Message: ".$session->getMessage());
					}
				$clickTracker = new Pap_Api_ClickTracker($session);
				try
					{
						$clickTracker->track();
					}
				catch (Exception $e)
					{
						die("Click tracker: ".$e->getMessage());
					}
				if ($clickTracker->getAffiliate() != null && $clickTracker->getAffiliate()->getValue('userid') != null)
				{
					$refferer_UID = $clickTracker->getAffiliate()->getValue('userid'); // prints affiliate userid
				}*/
				global $site_url, $site_name;
				$passwd = $ld['join_pass'];
				
				$this->dbu->query("
									INSERT INTO 
												trainer 
									SET 
												username='".$ld['join_email']."', 
												email='".$ld['join_email']."',
												password='".$passwd."', 
												create_date=NOW(), 
												is_trial='1', 
												expire_date='', 
												active = '1',
												affiliate_refferer_id = '".$refferer_UID."'
									");
				
				// mail here
		$message_data=get_sys_message('nmjoin');
        $ordermail = $ld['join_email'];
        $fromMail = $message_data['from_email'];
        $replyMail = $message_data['from_email'];

			$body=$message_data['text'];
	
			$body=str_replace('[!SITE_NAME!]', $site_name, $body );
			$body=str_replace('[!SITE_URL!]', $site_url, $body );
			$body=str_replace('[!USER_NAME!]', $ld['join_email'], $body );
			$body=str_replace('[!PASSWORD!]', $ld['join_pass'], $body );
			$body=str_replace('[!ADMIN_MAIL!]', $fromMail, $body );
			$body = nl2br($body);
                
        require_once ('class.phpmailer.php');        
        include_once ("classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

        $mail = new PHPMailer();
        //$body             = file_get_contents('contents.html');
        //$body             = eregi_replace("[\]",'',$body);
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Host = SMTP_HOST; // sets the SMTP server
        $mail->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
        $mail->Username = SMTP_USERNAME; // SMTP account username
        $mail->Password = SMTP_PASSWORD; // SMTP account password
        $mail->SetFrom($replyMail, $replyMail);
        $mail->AddReplyTo($replyMail, $replyMail);
        $mail->Subject = $message_data['subject'];
        
        //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
         
        $mail->MsgHTML($body);
        
        //$address = $receiver_email;
        $mail->AddAddress($ordermail, '');
        $mail->Send();
			$ld['error'] = 'Please check your email to see how to login!';
	
			return true;
			}

	 }

	function validate_join(&$ld)
		{
			$is_ok=true;
			
			if(!$ld['join_email'])
				{
					$ld['error'].="Please fill in the 'Email' field."."<br>";
					$is_ok=false;
				}
			if($ld['join_email'] && !secure_email($ld['join_email']))
				{
					$ld['error'].="Please provide a valid email address."."<br>";
					$is_ok=false;
				}
			if(!$ld['join_pass'])
				{
					$ld['error'].="Please fill in the 'Password' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['join_pass_repeat'])
				{
					$ld['error'].="Please fill in the 'repeat Password' field."."<br>";
					$is_ok=false;
				}
			if(strlen($ld['join_pass'])<7 || strlen($ld['join_pass_repeat'])<7)
				{
					$ld['error'].="The password must be over 6 characters long."."<br>";
					$is_ok=false;
				}
			if($ld['join_pass']!==$ld['join_pass_repeat'])
				{
					$ld['error'].="The password doesn't match."."<br>";
					$is_ok=false;
				}
			return $is_ok;
		}
	 
	function make_pass_pattern()
		{
			/* PREPARE A NEW RANDOM PATTERN FOR THE RANDOM PASSWORD */
			$pass_gen_pattern = array(
							"0" => "x",
							"1" => "X",
							"2" => "c",
							"3" => "C",
							"4" => "v",
							"5" => "V",
							"6" => "0",
							"7" => "*",
						);
			$pattern = "";
			$patt = rand(6,9);
			$i = 0;
			while($i<$patt)
				{
					if($i==0) $pattern .= $pass_gen_pattern[rand(0,5)];
					else $pattern .= $pass_gen_pattern[rand(0,6)];
					$i++;
				}
			return $pattern;
		}

	function forgotpass(&$ld)
	{
		if(!$this->validate_forgotpass($ld))
		{
			return false;
		}
			
		global $user_level;
	    
	    $this->dbu->query("
							SELECT 
								trainer_id, password, email
							FROM 
								trainer 
							WHERE 
								username = '".$ld['username']."'
						");
	    /* CHECK IF EMAIL EXIST IN DB, IF NOT, THROW ERR */
		if($this->dbu->move_next())
			{
				global $site_url, $site_name;
				
				// mail here
		$message_data=get_sys_message('fpne');
        $ordermail = $this->dbu->gf('email');
        $fromMail = $message_data['from_email'];
        $replyMail = $message_data['from_email'];

			$body=$message_data['text'];
	
			$body=str_replace('[!SITE_NAME!]', $site_name, $body );
			$body=str_replace('[!SITE_URL!]', $site_url, $body );
			$body=str_replace('[!USER_NAME!]', $ld['username'], $body );
			$body=str_replace('[!PASSWORD!]', $this->dbu->f('password'), $body );
			$body=str_replace('[!ADMIN_MAIL!]', $fromMail, $body );
			$body = nl2br($body);
                
        require_once ('class.phpmailer.php');        
        include_once ("classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

        $mail = new PHPMailer();
        //$body             = file_get_contents('contents.html');
        //$body             = eregi_replace("[\]",'',$body);
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Host = SMTP_HOST; // sets the SMTP server
        $mail->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
        $mail->Username = SMTP_USERNAME; // SMTP account username
        $mail->Password = SMTP_PASSWORD; // SMTP account password
        $mail->SetFrom($replyMail, $replyMail);
        $mail->AddReplyTo($replyMail, $replyMail);
        $mail->Subject = $message_data['subject'];
        
        //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
         
        $mail->MsgHTML($body);
        
        //$address = $receiver_email;
        $mail->AddAddress($ordermail, '');
        $mail->Send();

			$ld['error'] = 'Please check your email!';
	
			return true;

			}
			else 
			{
				$ld['error'] = 'This user does not exist!';
				return false;				
			}

	 }
		
	function validate_forgotpass(&$ld)
		{
			$is_ok=true;
			
			if(!$ld['username'])
				{
					$ld['error'].="Please fill in the 'Username' field."."<br>";
					$is_ok=false;
				}
			return $is_ok;
		}
		
/* MEMBER PROFILE SECTION */

	function update_licence(&$ld)
		{
			if(!$this->validate_update_licence($ld))
				{
					$ld['pag']= "profile_choose_clinic"; 
					return false;
				}
			if($ld['is_clinic']==0) $update = "is_clinic='".$ld['is_clinic']."'";
			else if($ld['is_clinic']==1) $update = "is_clinic='".$ld['is_clinic']."', clinic_name='".$ld['clinic_name']."'";
			
			$this->dbu->query("
								UPDATE 
									trainer 
								SET 
									".$update." 
								WHERE 
									trainer_id=".$_SESSION[U_ID]." 
							");
		
			$ld['error']="Licence Succesfully Saved.";

	    	return true;
		}
		
	function validate_update_licence(&$ld)
		{
			$is_ok=true;
/*	
			if(!$ld['clinic_name'])
				{
					$ld['error'].="Please fill in the 'Clinic Name' field."."<br>";
					$is_ok=false;
				}
*/
			return $is_ok;
		}

	function add_profile(&$ld)
		{
			if(!$this->validate_add_profile($ld))
				{
					$ld['pag']= "profile"; 
					return false;
				}
		$ld['profile_id']=$this->dbu->query_get_id("
													INSERT INTO 
																trainer_profile 
													SET 
																company_name='".$ld['company_name']."', 
																first_name='".$ld['first_name']."', 
																surname='".$ld['surname']."', 
																address='".$ld['address']."', 
																post_code = '".$ld['post_code']."',
																website = '".$ld['website']."',
																phone = '".$ld['phone']."',
																mobile = '".$ld['mobile']."',
																trainer_id = '".$_SESSION[U_ID]."'
													");
		$get_mail = $this->dbu->field("SELECT email FROM trainer WHERE 1=1 AND trainer_id = ".$_SESSION[U_ID]);
		$ld['header_id']=$this->dbu->query_get_id("
													INSERT INTO 
																trainer_header_paper 
													SET 
																company_name='".$ld['company_name']."', 
																first_name='".$ld['first_name']."', 
																surname='".$ld['surname']."', 
																address='".$ld['address']."', 
																post_code = '".$ld['post_code']."',
																website = '".$ld['website']."',
																phone = '".$ld['phone']."',
																mobile = '".$ld['mobile']."',
																email = '".$get_mail."',
																trainer_id = '".$_SESSION[U_ID]."',
																profile_id = '".$ld['profile_id']."'
													");
		
			$this->dbu->query("UPDATE trainer SET first_name='".$ld['first_name']."', surname='".$ld['surname']."', 
				profile_id=".$ld['profile_id']." WHERE trainer_id=".$_SESSION[U_ID]." ");
		
			$this->dbu->query("UPDATE trainer_profile SET email='".$get_mail."' WHERE 1=1 AND trainer_id=".$_SESSION[U_ID]." AND profile_id=".$ld['profile_id']." ");
			
			$ld['error']="Profile Succesfully added.";

			return true;
		}
		
	function validate_add_profile(&$ld)
		{
			$is_ok=true;
	
			if(!$ld['first_name'])
				{
					$ld['error'].="Please fill in the 'Company Name' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['surname'])
				{
					$ld['error'].="Please fill in the 'Company Name' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['address'])
				{
					$ld['error'].="Please fill in the 'Address' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['post_code'])
				{
					$ld['error'].="Please fill in the 'Post Code' field."."<br>";
					$is_ok=false;
				}
/*
			if(!$ld['mobile'])
				{
					$ld['error'].="Please fill in the 'Mobile' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['company_name'])
				{
					$ld['error'].="Please fill in the 'Company Name' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['website'])
				{
					$ld['error'].="Please fill in the 'website' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['phone'])
				{
					$ld['error'].="Please fill in the 'Phone' field."."<br>";
					$is_ok=false;
				}
*/				
			return $is_ok;
		}

	function update_email(&$ld)
		{
			if(!$this->validate_update_email($ld))
				{
					$ld['pag']= "profile_edit_email"; 
					return false;
				}

			$this->dbu->query("UPDATE trainer SET email='".$ld['email']."' WHERE trainer_id=".$_SESSION[U_ID]." ");
			$get_profile_id = $this->dbu->field("SELECT profile_id FROM trainer WHERE 1=1 AND trainer_id = ".$_SESSION[U_ID]);

			$this->dbu->query("UPDATE trainer_profile SET email='".$ld['email']."' WHERE 1=1 AND trainer_id=".$_SESSION[U_ID]." AND profile_id=".$get_profile_id." ");
		
			$ld['error']="Profile Email Succesfully changed.";

			return true;
		}
	
	function validate_update_email(&$ld)
		{
			$is_ok=true;
	
			if(!$ld['email'])
				{
					$ld['error'].="Please fill in the 'Email' field."."<br>";
					$is_ok=false;
				}

			if($ld['email'] && !secure_email($ld['email']))
				{
					$ld['error'].="Please provide a valid email address."."<br>";
					$is_ok=false;
				}

			return $is_ok;
		}

	function update_pass(&$ld)
		{
			if(!$this->validate_update_pass($ld))
				{
					$ld['pag']= "profile_edit_password"; 
					return false;
				}

		$this->dbu->query("UPDATE trainer SET password='".$ld['pass']."' WHERE trainer_id=".$_SESSION[U_ID]." ");
		
		$ld['error']="Profile Password Succesfully changed.";

	    return true;
		}
		
	function validate_update_pass(&$ld)
		{
			$is_ok=true;
			if(!$ld['old_pass'])
			{
					$ld['error'].="Please fill in the 'old password' field."."<br>";
					$is_ok=false;			
			}
			if(!$ld['pass'])
			{
					$ld['error'].="Please fill in the 'password' field."."<br>";
					$is_ok=false;			
			}
			if(!$ld['pass1'])
			{
					$ld['error'].="Please fill in the 'repeat password' field."."<br>";
					$is_ok=false;			
			}
			if($ld['old_pass'])
			{
				$this->dbu->query("select trainer.password from trainer where trainer.trainer_id='".$_SESSION[U_ID]."' ");
				
				$this->dbu->move_next();
				if($this->dbu->f('password')!=$ld['old_pass'])
				{
	                $ld['error'].="Old Password Doesn't match."."<br>";
	                $is_ok=false;
				}
			}		
	        if ($ld['pass']!=$ld['pass1'])
	        {
	                $ld['error'].="Password Doesn't match."."<br>";
	                $is_ok=false;
	        }
			return $is_ok;
		}

	function update_profile(&$ld)
		{
			if(!$this->validate_update_profile($ld))
				{
					$ld['pag']= "profile_edit"; 
					return false;
				}
		$this->dbu->query("							UPDATE 
																trainer_profile 
													SET 
																company_name='".$ld['company_name']."', 
																first_name='".$ld['first_name']."', 
																surname='".$ld['surname']."', 
																address='".$ld['address']."', 
																post_code = '".$ld['post_code']."',
																website = '".$ld['website']."',
																phone = '".$ld['phone']."',
																mobile = '".$ld['mobile']."' 
													WHERE trainer_id=".$_SESSION[U_ID]."
													");

			$this->dbu->query("UPDATE trainer SET first_name='".$ld['first_name']."', surname='".$ld['surname']."', 
				profile_id=".$ld['profile_id']." WHERE trainer_id=".$_SESSION[U_ID]." ");
		
		$ld['error']="Profile Succesfully changed.";

	    return true;
		}
		
	function update_profile_notes(&$ld){
		$this->dbu->query("SELECT trainer_id FROM trainer_profile WHERE trainer_id=".$_SESSION[U_ID]." ");
		if(!$this->dbu->move_next()){
			$ld['error']="Please fill your Contact information first.";
			return false;
		}
		$this->dbu->query("UPDATE trainer_profile SET exercise_notes = '".$ld['exercise_notes']."' WHERE trainer_id=".$_SESSION[U_ID]." ");
		$ld['error'] = 'Additional notes updated succesfully.';
		return true;		
	}
		
	function validate_update_profile(&$ld)
		{
			$is_ok=true;
	
			if(!$ld['first_name'])
				{
					$ld['error'].="Please fill in the 'First Name' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['surname'])
				{
					$ld['error'].="Please fill in the 'Surname' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['address'])
				{
					$ld['error'].="Please fill in the 'Address' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['post_code'])
				{
					$ld['error'].="Please fill in the 'Post Code' field."."<br>";
					$is_ok=false;
				}
/*				
			if(!$ld['mobile'])
				{
					$ld['error'].="Please fill in the 'Mobile' field."."<br>";
					$is_ok=false;
				}
			if(!$ld['phone'])
				{
					$ld['error'].="Please fill in the 'Phone' field."."<br>";
					$is_ok=false;
				}
			if($ld['phone'] && !secure_phone($ld['phone']))
				{
					$ld['error'].="Please provide a valid phone."."<br>";
					$is_ok=false;
				}
			if($ld['mobile'] && !secure_phone($ld['mobile']))
				{
					$ld['error'].="Please provide a valid mobile."."<br>";
					$is_ok=false;
				}
*/				
			return $is_ok;
		}
	
	function update_custom_header(&$ld)
		{
			if(!$this->validate_update_custom_header($ld))
				{
					$ld['pag']= "profile_header_paper"; 
					return false;
				}
			
			/*$this->dbu->query("
								UPDATE 
									trainer_header_paper 
								SET 
									company_name='".$ld['company_name']."',
									first_name='".$ld['first_name']."',
									surname='".$ld['surname']."',
									address='".$ld['address']."',
									post_code='".$ld['post_code']."',
									website='".$ld['website']."',
									phone='".$ld['phone']."',
									mobile='".$ld['mobile']."',
									email='".$ld['email']."',
									fax='".$ld['fax']."'
								WHERE 
									trainer_id='".$_SESSION[U_ID]."'");*/
			if(!$ld['delete_image']&&!empty($_FILES['upload_image']['name']))
				{
					$this->upload_file($ld);
					//$ld['error']='unchecked';
				}
			else 
				{
					$this->erasepicture($ld);
					//$ld['error']='checked';
				}
			$ld['error']="Header Paper Updated Successfully.";
			return true;
		}

	function validate_update_custom_header(&$ld){
		$is_ok= true;
/*
		if(!$ld['first_name']){
			$ld['error'] .= 'Please fill your first name.';
			$is_ok = false;
		}
		if(!$ld['surname']){
			$ld['error'] .= 'Please fill your surname.';
			$is_ok = false;
		}
		if(!$ld['address']){
			$ld['error'] .= 'Please fill your address.';
			$is_ok = false;
		}
		if(!$ld['post_code']){
			$ld['error'] .= 'Please fill your post code.';
			$is_ok = false;
		}
		if(!$ld['mobile']){
			$ld['error'] .= 'Please fill your post mobile.';
			$is_ok = false;
		}
*/		
		return $is_ok;
	}

	function upload_file(&$ld)
{
        global $_FILES, $script_path, $is_live;
        $allowed['.gif']=1;
        $allowed['.jpg']=1;
        $allowed['.jpeg']=1;
        $f_ext=substr($_FILES['upload_image']['name'],strrpos($_FILES['upload_image']['name'],"."));
        if(!$allowed[strtolower($f_ext)])
        {
        	$ld['error']="Only jpg, jpeg and gif files are accepted.";
        	return false;
        }
        
        if(!is_numeric($_SESSION[U_ID]))
        {
        	$ld['error'].="Error.".'<br>';
        	return false;
        }
        else 
        {
         	$this->dbu->query("SELECT trainer_id, logo_image FROM trainer_profile WHERE trainer_id='".$_SESSION[U_ID]."'");
        	if(!$this->dbu->move_next())
        	{
	        	$ld['error'].="Error.".'<br>';
	        	return false;
        	}
        	else 
        	{
        		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('logo_image') );
				$this->dbu->query("UPDATE trainer_profile SET logo_image=NULL WHERE trainer_id='".$_SESSION[U_ID]."'");
        	}
        }
		
        $f_title="headed_logo_".$_SESSION[U_ID].$f_ext;
        $f_out=$script_path.UPLOAD_PATH.$f_title;
        
        if(!$_FILES['upload_image']['tmp_name'])
        {
                 $ld['error'].="Please upload a file!"."<br>";
                 return false;
        }        
        if(!$is_live || (strtolower($f_ext) =='.gif'))
        {
        	if(FALSE === move_uploaded_file($_FILES['upload_image']['tmp_name'],$f_out))
	        {
	               // $ld['error'].="Unable to upload the file.  Move operation failed."."<!-- Check file permissions -->";
	                return false;
	        }
	        
        	$this->dbu->query("UPDATE trainer_profile SET
	                           logo_image='".$f_title."'
	                           WHERE trainer_id='".$_SESSION[U_ID]."'" 
	                          );
			@chmod($f_out, 0777);
        	$ld['error'].="Image Succesfully saved.<br>";
        	return true;
        }
        else
        {
        	
//        	$this->resize($_FILES['upload_image']['tmp_name'], 276, 0, $f_title);
        	$this->resize($_FILES['upload_image']['tmp_name'], 200, 0, $f_title);
	        @chmod($f_out, 0777);
        	$this->dbu->query("UPDATE trainer_profile SET
	                           logo_image='".$f_title."'
	                           WHERE trainer_id='".$_SESSION[U_ID]."'" 
	                          );
	        $ld['error'].="Image Succesfully saved.".'<br>';
	        return true;
        }
 
}
	/****************************************************************
	* function erasepicture(&$ld)                                   *
	****************************************************************/
	function erasepicture(&$ld)
{
      	$this->dbu->query("SELECT logo_image FROM trainer_profile WHERE trainer_id='".$_SESSION[U_ID]."'");
	    if(!$this->dbu->move_next())
	    {
	        $ld['error'].="Invalid ID.<br>";
	        return false;
	    }
	    else 
	    {
			global $script_path;
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('logo_image'));
			$this->dbu->query("UPDATE trainer_profile SET logo_image=NULL WHERE trainer_id='".$_SESSION[U_ID]."'");
	    }
	$ld['error'] .= "Image Succesfully deleted!<br>";
	return true;
}
	/****************************************************************
	* function resize(&$ld)                                         *
	****************************************************************/

	function resize($original_image, $new_width, $new_height, $image_title) 
	{
		global $script_path;
		$original_image=ImageCreateFromJPEG($original_image);
		$aspect_ratio = imagesx($original_image) / imagesy($original_image); 
		if (empty($new_width)) 
		{ 
			$new_width = $aspect_ratio * $new_height; 
		}
		elseif (empty($new_height)) 
		{ 
			$new_height= $new_width / $aspect_ratio; 
		}
		if (imageistruecolor($original_image))	
		{ 
			$image = imagecreatetruecolor($new_width, $new_height); 
		} 
		else 
		{ 
			$image = imagecreate($new_width, $new_height); 
		} 
		// copy the original image onto the smaller blank 
		imagecopyresampled($image, $original_image, 0, 0, 0, 0, $new_width, $new_height, imagesx($original_image), imagesy($original_image));
		ImageJPEG($image, $script_path.UPLOAD_PATH.$image_title) or die("Problem In saving"); 
	}

function pay(&$ld){
	if(!$this->validate_pay($ld)){		
		return false;
	}	
	include_once('misc/CreateRecurringPaymentsProfile.php');
	
$this->dbu->query("select price_plan.* from price_plan where price_id='".$ld['price_id']."' ");

$this->dbu->move_next();

	$firstName = urlencode($ld['first_name']);
    $lastName = urlencode($ld['surname']);
    $country = urlencode(get_country_code($ld['country_id']));
    
    $creditCardType = urlencode($ld['credit_card_type']);
    $creditCardNumber = urlencode($ld['credit_card_no']);
    $expDateMonth = $ld['e_month'];
    // Month must be padded with leading zero
    $padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
    $expDateYear = urlencode($ld['e_year']);
    $cvv2Number = urlencode($ld['cvv2']);
	
    $paymentAmount = urlencode($this->dbu->f('price_value'));
    $currencyID = urlencode($this->dbu->f('currency'));			// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
    //set the start billing date to now
	$randTime = rand(400,999);

    $timestamp = time()+$randTime;
//    $next_timestamp = $timestamp + (1 * 24 * 60 * 60);
    $next_timestamp = strtotime('+1 '.$this->dbu->f('licence_period'))+$randTime;
    
    $dateTime = date('Y-m-d', $timestamp).'T'.date('H:i:s', $timestamp).'Z';
    $next_dateTime = date('Y-m-d', $next_timestamp).'T'.date('H:i:s', $next_timestamp).'Z';
    $startDate = urlencode($dateTime);
    $nextDate = urlencode($next_dateTime);
    $email = urlencode($ld['email']);
    
    $billingPeriod = urlencode(ucfirst($this->dbu->f('licence_period')));				// or "Day", "Week", "SemiMonth", "Year"
    $billingFreq = urlencode("1"); 	
//    $billingFreq = urlencode("365");  // combination of this and billingPeriod must be at most a year
    
    $desc = urlencode("Testing");
    $maxFailedPayments=1;
    
    
//	$nvpStr = "&INITAMT=$paymentAmount&AUTOBILLOUTAMT=AddToNextBilling&FAILEDINITAMTACTION=CancelOnFailure&NEXTBILLINGDATE=$nextDate&EMAIL=$email";
	$nvpStr = "&AUTOBILLOUTAMT=AddToNextBilling&FAILEDINITAMTACTION=CancelOnFailure&NEXTBILLINGDATE=$nextDate&EMAIL=$email";
		
	$nvpStr.="&AMT=$paymentAmount&CURRENCYCODE=$currencyID&PROFILESTARTDATE=$startDate".
            "&BILLINGPERIOD=$billingPeriod&BILLINGFREQUENCY=$billingFreq&MAXFAILEDPAYMENTS=$maxFailedPayments&DESC=$desc".
            "&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
            "&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName&COUNTRYCODE=$country";

$expireDate = $this->dbu->field("select expire_date from trainer where trainer_id=".$_SESSION[U_ID]." ");

$theTime = $next_timestamp+(strtotime($expireDate)-time('now'));
$theExpireTime = date('Y-m-d H:i:s',$theTime);

    $httpParsedResponseAr = PPHttpPost('CreateRecurringPaymentsProfile', $nvpStr);

   if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {

   	 $this->dbu->query("UPDATE trainer 
   	 					SET 
							paypal_profile_id = '".urldecode($httpParsedResponseAr['PROFILEID'])."',
							country_id 	    = '".$ld['country_id']."',
							price_plan_id 	= '".$ld['price_id']."',
							is_trial		= '0',
							expire_date		= '$theExpireTime'
						WHERE 
							trainer_id=".$_SESSION[U_ID]."
											");
   	 $affiliate_UID = $this->dbu->query("
   	 					SELECT 
   	 						username, password, affiliate_refferer_id
   	 					FROM 
   	 						trainer
   	 					WHERE 
							trainer_id=".$_SESSION[U_ID]."
   	 										");
   	 $affiliate_UID->next();
				require_once ('misc/PapApi.class.php');
				// login (as merchant)
				
				$session = new Gpf_Api_Session(AFFILIATES_API_M_URL);
				if(!$session->login(AFFILIATES_API_M_USERNAME, AFFILIATES_API_M_PASSWORD))
					{
						die("Cannot login. Message: ".$session->getMessage());
					}

                $affiliate = new Pap_Api_Affiliate($session);
                $affiliate->setUsername($affiliate_UID->f('username'));
                $affiliate->setPassword($affiliate_UID->f('password'));
                $affiliate->setFirstname($ld['first_name']);
                $affiliate->setLastname($ld['surname']);
				$refId = substr(md5($affiliate_UID->f('username')),0,20);

                $affiliate->setRefid($refId);
                $affiliate->setParentUserId(''.$affiliate_UID->f('affiliate_refferer_id').'');
                $affiliate->setStatus('A');

    try {  
                if($affiliate->add()) {
				   	  $ld['error'] .= "Affiliate Register Succeded"."<br>";
                } else {
				   	  $ld['error'] .= "Affiliate Register Failed"."<br>";
                }
            } catch(Exception $e) {
//                die("new user: ".$e->getMessage());
				   	  $ld['error'] .= $e->getMessage()."<br>";
                return;
            }
   	  $ld['error'] .= "Register Succeded"."<br>";
    return true;
	} else{
	  $ld['error'] .= urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);
		echo '<pre>';
	    print_r($httpParsedResponseAr);
	    echo '</pre>';
		echo '<pre>';
	    print_r($nvpStr);
	    echo '</pre>';
	    return false;
	 	}	
	
}

/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/
function validate_pay(&$ld)
{
	$is_ok = true;
	if(!$ld['first_name']){
		$ld['error'] .= 'Please fill the First Name'."<br>";
	   $is_ok = false;
	}
	if(!$ld['surname']){
		$ld['error'] .= 'Please fill the Surname'."<br>";
	   $is_ok = false;
	}
	if(!$ld['email']){
		$ld['error'] .= 'Please fill the Email'."<br>";
		$is_ok = false;
	}	
	elseif(!secure_email($ld['email']))
		{
		$ld['error'] .= 'Invalid Email format'."<br>";
			
			$is_ok=false;
		}
	if(!$ld['country_id'])
		{
		$ld['error'] .= 'Please serlect the Country'."<br>";
		
			$is_ok=false;
		}	
	
	
	if(!$ld['credit_card_type']){
		$ld['error'] .= 'Please select credit card type'."<br>";
	   $is_ok = false;
	}
	if(!$ld['credit_card_no']){
		$ld['error'] .= 'Please fill the Credit Card Number'."<br>";
	   $is_ok = false;
	}	
		
    return $is_ok;
}

function cancel_payment(&$ld)
	{
		if(!$ld['pp_del_key']){
			return false;
		}elseif ($ld['pp_del_key']!='123delkey321'){
			return false;
		}
		$this->dbu->query("SELECT * FROM trainer WHERE trainer_id=".$_SESSION[U_ID]." ");
		if(!$this->dbu->move_next()){
			return false;
		}
		else
		{
		include_once('misc/CreateRecurringPaymentsProfile.php');
		$nvpStr = "&PROFILEID=".urlencode($this->dbu->f('paypal_profile_id'))."&ACTION=Cancel";

	    $httpParsedResponseAr = PPHttpPost('ManageRecurringPaymentsProfileStatus', $nvpStr);
	    
   if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {

   	 $this->dbu->query("UPDATE trainer 
   	 					SET 
							paypal_profile_id = '',
							country_id 	    = '0',
							price_plan_id 	= '0'
						WHERE 
							trainer_id=".$_SESSION[U_ID]."
											");
   	 
		$ld['error'] ='Recurring payment has been canceled<br />';
    return true;
	} else{
	  $ld['error'] .= urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);
/*		echo '<pre>';
	    print_r($httpParsedResponseAr);
	    echo '</pre>';
		echo '<pre>';
	    print_r($nvpStr);
	    echo '</pre>';
*/	    return false;
	 	}	
		}
	}

	function delete_profile(&$ld)
		{
			$ld['error']="Not implemented yet.";
		    return true;
		}

}//end class