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
				global $site_url, $site_name;
				require_once("misc/cls_pwd_gen.php"); // LOAD THE PASSWORD GENERATOR CLASS
				$pattern = $this->make_pass_pattern();
				$pwd = new pwdGen();
				//$pwd->pattern = "cCVCxcc"; //optional
				$pwd->pattern = $pattern; //optional
				$the_random_passwd = $pwd->newPwd();
				
				$this->dbu->query("
									INSERT INTO 
												trainer 
									SET 
												username='".$ld['join_email']."', 
												email='".$ld['join_email']."',
												password='".$the_random_passwd."', 
												create_date=NOW(), 
												is_trial='1', 
												expire_date='', 
												active = '1' 
									");
				
				// mail here
 		   $mail_to = $ld['join_email'];
           $mail_from=ADMIN_EMAIL;
           $body='Thank you for joining to '.$site_name.' website

Login Informations:
WebSite URL: '.$site_url.'
Username: '.$mail_to.'
Password: '.$the_random_passwd.'
           
For other informations you can contact us from the site Contact Us page 
or
directly at:
'.$mail_from.'
';
           
           
           $header.= "From: ".$mail_from." \n";
		   $header.= "Content-Type: text\n";
		   $mail_subject=$site_name." - account infos";		   
           @mail ( $mail_to , $mail_subject, $body , $header);				
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
 		   $mail_to = $this->dbu->f('email');
           $mail_from=ADMIN_EMAIL;
           $body=''.$site_name.' website Password Reminder

Login Informations:
WebSite URL: '.$site_url.'
Username: '.$ld['username'].'
Password: '.$this->dbu->f('password').'
           
For other informations you can contact us from the site Contact Us page 
or
directly at:
'.$mail_from.'
';
           
           
           $header.= "From: ".$mail_from." \n";
		   $header.= "Content-Type: text\n";
		   $mail_subject=$site_name." - I forgot my password";		   
           @mail ( $mail_to , $mail_subject, $body , $header);				
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
	function add_profile(&$ld)
		{
			if(!$this->validate_add_profile($ld))
				{
					$ld['pag']= "profile_add"; 
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
			if(!$ld['mobile'])
				{
					$ld['error'].="Please fill in the 'Mobile' field."."<br>";
					$is_ok=false;
				}
/*
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
		$ld['profile_id']=$this->dbu->query_get_id("
													UPDATE 
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
		
	function validate_update_profile(&$ld)
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
			if(!$ld['mobile'])
				{
					$ld['error'].="Please fill in the 'Mobile' field."."<br>";
					$is_ok=false;
				}
/*				
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
			
			$this->dbu->query("
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
									email='".$ld['email']."'
								WHERE 
									trainer_id='".$_SESSION[U_ID]."'");
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
         	$this->dbu->query("SELECT trainer_id, logo_image FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");
        	if(!$this->dbu->move_next())
        	{
	        	$ld['error'].="Error.".'<br>';
	        	return false;
        	}
        	else 
        	{
        		@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('logo_image') );
				$this->dbu->query("UPDATE trainer_header_paper SET logo_image=NULL WHERE trainer_id='".$_SESSION[U_ID]."'");
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
	        
        	$this->dbu->query("UPDATE trainer_header_paper SET
	                           logo_image='".$f_title."'
	                           WHERE trainer_id='".$_SESSION[U_ID]."'" 
	                          );
			@chmod($f_out, 0777);
        	$ld['error'].="Image Succesfully saved.<br>";
        	return true;
        }
        else
        {
        	
        	$this->resize($_FILES['upload_image']['tmp_name'], 275, 0, $f_title);
	        @chmod($f_out, 0777);
        	$this->dbu->query("UPDATE trainer_header_paper SET
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
      	$this->dbu->query("SELECT logo_image FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");
	    if(!$this->dbu->move_next())
	    {
	        $ld['error'].="Invalid ID.<br>";
	        return false;
	    }
	    else 
	    {
			global $script_path;
			@unlink( $script_path.UPLOAD_PATH.$this->dbu->f('logo_image'));
			$this->dbu->query("UPDATE trainer_header_paper SET logo_image=NULL WHERE trainer_id='".$_SESSION[U_ID]."'");
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
	if(!$ld['member_id']){
		$ld['error'] ='There was an error processing your request';
		return false;
	}
	if(!$ld['plan']){
		$ld['error'] = 'Please select a payment plan.';
		return false;
	}
	if(!$ld['currency']){
		$ld['error'] ='Please select your currency.';
		return false;
	}
	//if we reach this point we updat the client
	$this->dbu->query("UPDATE member SET type=?,curency=?,payment_plan=? WHERE member_id = ?",array($ld['type'],$ld['currency'],$ld['plan'],$ld['member_id']));//member has been updated
	//we get the plan info out so we know how much to charge the dude
	$planInfo = $this->dbu->row("SELECT * FROM account_type WHERE account_type_id=?",$ld['type']);
	$currency = array('0','GBP','USD','EUR');
	include_once('classes/cls_paypal.php');
	$paypal = new paypal();

	
	if(isset($ld['payviapaypal_x']) || isset($ld['payviapaypal'])){
		global $site_ssl;
		$param = array('site_url'=>$site_ssl,
						'amount' => $ld['plan'] == YEARLY_PLAN ? $planInfo[$currency[$ld['currency']]]* $planInfo['monthspayed'] : $planInfo[$currency[$ld['currency']]],
						'currencyCode' => $currency[$ld['currency']],
						'returnURLParams'=> 'member='.$ld['member_id'],
						'member' => $ld['member_id'],
						'cancelURL' =>$site_ssl.'index.php?pag=register2&member_id='.$ld['member_id'].'&paypalfail=1');
		if(!$paypal->expressCheckout($param)){
			$ld['error'] = $param['error'];
			return false;
		}
	}else{
		$checkFields = array('card'=>'Card Type','card_number'=>'Card Number','card_exp_month'=>'Expiry Date','card_exp_year'=>'Expiry Date','cvv'=>'Card Verification Number');
		foreach ($checkFields as $field=>$fieldLabel){
			if(!$ld[$field]){
				$ld['error'] = 'Please fill in the \''.$fieldLabel.'\' field.';
				return false;
			}
		}
		
		//we get the clientinfo and build the param array to pass to paypal :)		
		$memberInfo = $this->dbu->query("SELECT member.*,country.code AS countryCODE FROM member 
										 INNER JOIN country ON country.name = member.country
		WHERE member.member_id = ?",$ld['member_id']);
		if(!$memberInfo->next()){
			$ld['error'] .='Could not find user';
			return false;
		}
		$param =array(
			'first_name' => $memberInfo->f('first_name'),
			'last_name'	 => $memberInfo->f('last_name'),
			'creditCardType' => $ld['card'],
			'creditCardNumber' => $ld['card_number'],
			'expDateMonth' => $ld['card_exp_month'],
			'expDateYear' => $ld['card_exp_year'],
			'cvv2Number' => $ld['cvv'],
			'address1' => $memberInfo->f('address1'),
			'address2' => $memberInfo->f('address2'),
			'city' => $memberInfo->f('town'),
			'state' =>$memberInfo->f('state'),
			'zip'=> $memberInfo->f('zip'),
			'country' => $memberInfo->f('countryCODE'),
			'amount' => $ld['plan'] == YEARLY_PLAN ? $planInfo[$currency[$ld['currency']]]* $planInfo['monthspayed'] : $planInfo[$currency[$ld['currency']]],
			'period' => $ld['plan'] == YEARLY_PLAN ? 'YEAR' : 'MONT',
			'term' => $ld['plan'] == YEARLY_PLAN  ? $planInfo['monthspayed'] : 12,
			'currencyCode' => $currency[$ld['currency']]
		);
		if(!$paypal->recurringPayment($param)){
			$ld['error'] = $param['error'];
			return false;
		}
		$this->dbu->query("UPDATE member SET expire='".strtotime('+1 year')."',paid=1,profileid = '".$param['PROFILEID']."' WHERE member_id = ?",$ld['member_id']);
		$ld['success'] = '1';
		$ld['pag'] = 'login';
	}
	return true;
}

	function cancelRecurring(&$ld){
		if(!$ld['key']){
			return false;
		}elseif ($ld['key']!='123456remove654321'){
			return false;
		}
		if(!$ld['m']){
			return false;
		}
		$this->dbu->query("SELECT profileid FROM member WHERE member_id = ?",$ld['m']);
		if(!$this->dbu->move_next()){
			return false;
		}
		include_once('classes/cls_paypal.php');
		$paypal = new paypal();
		if(!$paypal->cancelRecurring(array('profileid'=>$this->dbu->f('profileid')))){
			$ld['error'] = $param['error'];
			return false;
		}
		$ld['error'] ='Recurring payment has been canceled<br />';
		return true;
	}

	function delete_profile(&$ld)
		{
			$ld['error']="Not implemented yet.";
		    return true;
		}

}//end class