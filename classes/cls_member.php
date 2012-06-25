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
			
			$this->dbu->query("
							SELECT 
								*
							FROM 
								settings
							WHERE constant_name='PAYPAL_USERNAME' OR constant_name='PAYPAL_PASSWORD' OR constant_name='PAYPAL_SIGN'
							
						");
			while($this->dbu->move_next())
			{
				if($this->dbu->f('constant_name') == 'PAYPAL_USERNAME')
				{
					define('API_USERNAME', $this->dbu->f('value'));
				}
				elseif($this->dbu->f('constant_name') == 'PAYPAL_PASSWORD')
				{
					define('API_PASSWORD', $this->dbu->f('value'));
				}
				elseif($this->dbu->f('constant_name') == 'PAYPAL_SIGN')
				{
					define('API_SIGN', $this->dbu->f('value'));
				}
			}
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
            $ld['error'] = get_template_tag('affiliate', $ld['lang'], 'T.USER_EXIST');
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
                                            affiliate_refferer_id = '".$refferer_UID."',
                                            lang = '".$ld['join_language']."'
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
			$ld['error'] = get_template_tag('affiliate', $ld['lang'], 'T.CHECK_EMAIL');
		
            $registerEmail = 'info@rehabmypatient.com';
            //$registerEmail = 'ole_gi@miralex.com.ua';
            $mail1 = new PHPMailer();
            $mail1->IsSMTP(); // telling the class to use SMTP
            $mail1->SMTPDebug = 1; // enables SMTP debug information (for testing)
            $mail1->SMTPAuth = true; // enable SMTP authentication
            $mail1->Host = SMTP_HOST; // sets the SMTP server
            $mail1->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
            $mail1->Username = SMTP_USERNAME; // SMTP account username
            $mail1->Password = SMTP_PASSWORD; // SMTP account password
            $body = 'User '.$ld['join_email'].' has been registered. Check admin panel.';
            $mail1->SetFrom($registerEmail, $registerEmail);
            $mail1->Subject = 'New user registered.';
            $mail1->MsgHTML($body);
            $mail1->AddAddress($registerEmail, '');
            $mail1->Send();
	
            return true;
		}

	 }

	function validate_join(&$ld)
		{
			$is_ok=true;
			
			if(!$ld['join_email'])
				{
					$ld['error'].=get_template_tag('affiliate', $ld['lang'], 'T.FILL_EMAIL')."<br>";
					$is_ok=false;
				}
			if($ld['join_email'] && !secure_email($ld['join_email']))
				{
					$ld['error'].=get_template_tag('affiliate', $ld['lang'], 'T.PROVIDE_EMAIL')."<br>";
					$is_ok=false;
				}
			if(!$ld['join_pass'])
				{
					$ld['error'].=get_template_tag('affiliate', $ld['lang'], 'T.FILL_PASS')."<br>";
					$is_ok=false;
				}
			if(!$ld['join_pass_repeat'])
				{
					$ld['error'].=get_template_tag('affiliate', $ld['lang'], 'T.FILL_REPEAT')."<br>";
					$is_ok=false;
				}
			if(strlen($ld['join_pass'])<5 || strlen($ld['join_pass_repeat'])<5)
				{
					$ld['error'].=get_template_tag('affiliate', $ld['lang'], 'T.OVER_5')."<br>";
					$is_ok=false;
				}
			if($ld['join_pass']!==$ld['join_pass_repeat'])
				{
					$ld['error'].=get_template_tag('affiliate', $ld['lang'], 'T.NOT_MATCH')."<br>";
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

			$ld['error'] = get_template_tag($ld['pag'], $ld['lang'], 'T.CHECK_EMAIL');
	
			return true;

			}
			else 
			{
				$ld['error'] = get_template_tag($ld['pag'], $ld['lang'], 'T.USER_NOT_EXIST');
				return false;				
			}

	 }
		
	function validate_forgotpass(&$ld)
		{
			$is_ok=true;
			
			if(!$ld['username'])
				{
					$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_USER')."<br>";
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
		
		$this->dbu->query("SELECT first_name, surname, username FROM trainer WHERE trainer_id='".$_SESSION[U_ID]."' ");
		$this->dbu->move_next();
		$this->dbu->query("INSERT INTO trainer_header_paper SET trainer_id='".$_SESSION[U_ID]."', first_name='".$this->dbu->f('first_name')."', surname='".$this->dbu->f('surname')."', email='".$this->dbu->f('username')."' ");
    
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
			$ld['pag'] = "profile";
			return false;
		}
		$profile_id = $this->dbu->query_get_id("
											INSERT INTO 
														trainer_profile 
											SET 
														company_name='".$ld['company_name']."', 
														first_name='".$ld['first_name']."', 
														surname='".$ld['surname']."', 
														address='".$ld['address']."', 
														city='".$ld['city']."', 
														post_code = '".$ld['post_code']."',
														website = '".$ld['website']."',
														phone = '".$ld['phone']."',
														mobile = '".$ld['mobile']."',
														trainer_id = '".$_SESSION[U_ID]."'
											");

		if(!$profile_id)
		{
			$this->dbu->query("select trainer.*, trainer_profile.* from trainer 
				INNER JOIN trainer_profile ON trainer.profile_id=trainer_profile.profile_id
			where trainer.trainer_id=".$_SESSION[U_ID]." ");
			$this->dbu->move_next();
			$profile_id = $this->dbu->f('profile_id');
		}
		
		$get_mail = $this->dbu->field("SELECT email FROM trainer WHERE 1=1 AND trainer_id = ".$_SESSION[U_ID]);

		$ld['header_id']=$this->dbu->query_get_id("
													INSERT INTO 
																trainer_header_paper 
													SET 
																company_name='".$ld['company_name']."', 
																first_name='".$ld['first_name']."', 
																surname='".$ld['surname']."', 
																address='".$ld['address']."', 
																city='".$ld['city']."', 
																post_code = '".$ld['post_code']."',
																website = '".$ld['website']."',
																phone = '".$ld['phone']."',
																mobile = '".$ld['mobile']."',
																email = '".$get_mail."',
																trainer_id = '".$_SESSION[U_ID]."',
																profile_id = '".$profile_id."'
													");

			$this->dbu->query("UPDATE trainer SET first_name='".$ld['first_name']."', surname='".$ld['surname']."', 
				profile_id=".$profile_id." WHERE trainer_id=".$_SESSION[U_ID]." ");

			$this->dbu->query("UPDATE trainer_profile SET email='".$get_mail."' WHERE 1=1 AND trainer_id=".$_SESSION[U_ID]." AND profile_id=".$profile_id." ");
			$ld['profile_id'] = $profile_id;
			$ld['error']=get_template_tag('profile_add', $ld['lang'], 'T.PROFILE_ADDED');
			
			return true;
		}
		
	function validate_add_profile(&$ld)
		{
			$is_ok=true;
	
			if(!$ld['first_name'])
				{
					$ld['error'].=get_template_tag('profile_add', $ld['lang'], 'T.FILL_FIRST')."<br>";
					$is_ok=false;
				}
			if(!$ld['surname'])
				{
					$ld['error'].=get_template_tag('profile_add', $ld['lang'], 'T.FILL_SURNAME')."<br>";
					$is_ok=false;
				}
			if(!$ld['city'])
				{
					$ld['error'].=get_template_tag('profile_add', $ld['lang'], 'T.FILL_CITY')."<br>";
					$is_ok=false;
				}
			if(!$ld['address'])
				{
					$ld['error'].=get_template_tag('profile_add', $ld['lang'], 'T.FILL_ADDRESS')."<br>";
					$is_ok=false;
				}
			if(!$ld['post_code'])
				{
					$ld['error'].=get_template_tag('profile_add', $ld['lang'], 'T.FILL_POST')."<br>";
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
		$changed_email = $this->dbu->field("SELECT email FROM trainer WHERE trainer_id=".$_SESSION[U_ID]." ");
		$ld['changed_email'] = $changed_email;
		
		if(!$this->validate_update_email($ld))
			{
				$ld['pag']= "profile_edit_email"; 
				return false;
			}

		//check has profile username
		$username = $this->dbu->field("SELECT username FROM trainer WHERE 1=1 AND trainer_id = ".$_SESSION[U_ID]);
		if(!$username)
			$this->dbu->query("UPDATE trainer SET username='".$ld['email']."' WHERE trainer_id=".$_SESSION[U_ID]." ");

		$this->dbu->query("UPDATE trainer SET email='".$ld['email']."' WHERE trainer_id=".$_SESSION[U_ID]." ");
		$get_profile_id = $this->dbu->field("SELECT profile_id FROM trainer WHERE 1=1 AND trainer_id = ".$_SESSION[U_ID]);

		$this->dbu->query("UPDATE trainer_profile SET email='".$ld['email']."' WHERE 1=1 AND trainer_id=".$_SESSION[U_ID]." AND profile_id=".$get_profile_id." ");
	
		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS_EMAIL');
		$_SESSION[USER_EMAIL] = $ld['email'];
		
		if($this->dbu->field("SELECT is_clinic FROM trainer WHERE trainer_id = ".$_SESSION[U_ID]) == 2)
		{
			$ld['pag']= "profile_choose_clinic"; 
		}
		
		return true;
	}
	
	function validate_update_email(&$ld)
	{
		$is_ok=true;

		if(!$ld['email'])
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_EMAIL')."<br>";
				$is_ok=false;
			}

		if($ld['email'] && !secure_email($ld['email']))
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.PROVIDE_EMAIL')."<br>";
				$is_ok=false;
			}

		return $is_ok;
	}

	function update_pass(&$ld)
	{
		$changed_pass = $this->dbu->field("SELECT password FROM trainer WHERE trainer_id=".$_SESSION[U_ID]." ");
		$ld['changed_pass'] = $changed_pass;
		
		if(!$this->validate_update_pass($ld))
		{
			$ld['pag']= "profile_edit_password"; 
			return false;
		}

		$this->dbu->query("UPDATE trainer SET password='".$ld['pass']."' WHERE trainer_id=".$_SESSION[U_ID]." ");
		
		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS_PASS');
	
		return true;
	}
		
	function validate_update_pass(&$ld)
		{
			$is_ok=true;
			if($ld['changed_pass'] && !$ld['old_pass'])
			{
					$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_OLD')."<br>";
					$is_ok=false;			
			}
			if(!$ld['pass'])
			{
					$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_PASS')."<br>";
					$is_ok=false;			
			}
			if(!$ld['pass1'])
			{
					$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_REPEAT')."<br>";
					$is_ok=false;			
			}
			if($ld['old_pass'])
			{
				$this->dbu->query("select trainer.password from trainer where trainer.trainer_id='".$_SESSION[U_ID]."' ");
				
				$this->dbu->move_next();
				if($this->dbu->f('password')!=$ld['old_pass'])
				{
	                $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.OLD_NOT_MATCH')."<br>";
	                $is_ok=false;
				}
			}		
	        if ($ld['pass']!=$ld['pass1'])
	        {
	                $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.PASS_NOT_MATCH')."<br>";
	                $is_ok=false;
	        }
	        if(strlen($ld['pass'])<5)
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.OVER_5')."<br>";
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
			
		if(!$ld['profile_id'])
		{
			$this->dbu->query("select trainer.*, trainer_profile.* from trainer 
				INNER JOIN trainer_profile ON trainer.profile_id=trainer_profile.profile_id
			where trainer.trainer_id=".$_SESSION[U_ID]." ");
			$this->dbu->move_next();
			$ld['profile_id'] = $this->dbu->f('profile_id');
		}

		//		
		$this->dbu->query("							UPDATE 
																trainer_profile 
													SET 
																company_name='".$ld['company_name']."', 
																first_name='".$ld['first_name']."', 
																surname='".$ld['surname']."', 
																address='".$ld['address']."', 
																city='".$ld['city']."', 
																post_code = '".$ld['post_code']."',
																website = '".$ld['website']."',
																phone = '".$ld['phone']."',
																mobile = '".$ld['mobile']."' 
													WHERE trainer_id=".$_SESSION[U_ID]."
													");

			$this->dbu->query("UPDATE trainer SET first_name='".$ld['first_name']."', surname='".$ld['surname']."', 
				profile_id=".$ld['profile_id']." WHERE trainer_id=".$_SESSION[U_ID]." ");

		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS');
		//$ld['pag'] = "profile";
	    return true;
	}
		
	function update_profile_notes(&$ld){

		$this->dbu->query("UPDATE trainer SET title_set='".$ld['title_set']."' WHERE trainer_id='".$_SESSION[U_ID]."'");
		
		$this->dbu->query("UPDATE  trainer SET lang='".$ld['language']."' WHERE trainer_id='".$_SESSION[U_ID]."'");
		//check exists in db
		$this->dbu->query("SELECT exercise_note_id FROM exercise_notes WHERE trainer_id=".$_SESSION[U_ID]." ");
		if(!$this->dbu->move_next()){
			$this->dbu->query("INSERT INTO exercise_notes SET exercise_notes = '".mysql_real_escape_string($ld['exercise_notes'])."', trainer_id=".$_SESSION[U_ID]." ");
		}
		else
		{
			$this->dbu->query("UPDATE exercise_notes SET exercise_notes = '".mysql_real_escape_string($ld['exercise_notes'])."' WHERE trainer_id=".$_SESSION[U_ID]." ");
		}
		$ld['error'] = get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS_NOTES');
		if($_COOKIE['language'] != $ld['language'])
		{
			setcookie('language', $ld['language'], 0, '/');
			$lang = ($ld['language'] == 'us') ? 'us/' : '';
			
			header("location: /".$lang.'index.php?pag=dashboard&redirect=1');
			exit;
		}
		
		return true;		
	}
		
	function validate_update_profile(&$ld)
	{
		$is_ok=true;

		if(!$ld['first_name'])
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_FIRST')."<br>";
				$is_ok=false;
			}
		if(!$ld['surname'])
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_SURNAME')."<br>";
				$is_ok=false;
			}
		if(!$ld['address'])
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_ADDRESS')."<br>";
				$is_ok=false;
			}
		if(!$ld['city'])
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_CITY')."<br>";
				$is_ok=false;
			}
		if(!$ld['post_code'])
			{
				$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_POST')."<br>";
				$is_ok=false;
			}
		return $is_ok;
	}
	
	function update_custom_header(&$ld)
	{
		$this->check_header_paper_exists();
		
		$this->dbu->query("
							UPDATE 
								trainer_header_paper 
							SET 
								company_name='".$ld['company_name']."',
								address='".$ld['address']."',
								first_name='".$ld['first_name']."',
								surname='".$ld['surname']."',
								post_code='".$ld['post_code']."',
								website='".$ld['website']."',
								phone='".$ld['phone']."',
								mobile='".$ld['mobile']."',
								email='".$ld['email']."',
								city='".$ld['city']."',
								fax='".$ld['fax']."',
								state_zip='".$ld['state_zip']."'
							WHERE 
								trainer_id='".$_SESSION[U_ID]."'");
		
		if(!$ld['delete_image']&&!empty($_FILES['upload_image']['name']))
		{
			if($_FILES['upload_image']['error'] === 0)
				$this->upload_custom_file($ld);
			//$ld['error']='unchecked';
		}
		elseif($ld['delete_image'])
		{
			$this->erasecustompicture($ld);
			//$ld['error']='checked';
		}
		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS');
		$ld['pag'] = 'dashboard';
		return true;
	}
		
	function check_header_paper_exists()
	{
		$this->dbu->query("SELECT header_id FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."' ");
		$this->dbu->move_next();
		if(!$this->dbu->f('header_id'))
		{
			$this->dbu->query("SELECT first_name, surname FROM trainer WHERE trainer_id='".$_SESSION[U_ID]."' ");
			$this->dbu->move_next();
			
			$this->dbu->query("INSERT INTO trainer_header_paper SET trainer_id='".$_SESSION[U_ID]."', first_name='".$this->dbu->f('first_name')."', surname='".$this->dbu->f('last_name')."' ");
			
		}
	}

	function validate_update_custom_header(&$ld){
		$is_ok= true;

		if(!$ld['first_name']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_FIRST');
			$is_ok = false;
		}
		if(!$ld['surname']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_SURNAME');
			$is_ok = false;
		}
		if(!$ld['address']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_ADDRESS');
			$is_ok = false;
		}
		if(!$ld['post_code']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_POST');
			$is_ok = false;
		}
		if(!$ld['mobile']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_MOBILE');
			$is_ok = false;
		}
		
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

	function upload_custom_file(&$ld)
	{
        global $_FILES, $script_path, $is_live;
        $allowed['.gif']=1;
        $allowed['.jpg']=1;
        $allowed['.jpeg']=1;
        $f_ext=substr($_FILES['upload_image']['name'],strrpos($_FILES['upload_image']['name'],"."));
        if(!$allowed[strtolower($f_ext)])
        {
            $ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.ONLY');
            return false;
        }
        
        if(!is_numeric($_SESSION[U_ID]))
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.ERROR').'<br>';
            return false;
        }
        else 
        {
            $this->dbu->query("SELECT trainer_id, logo_image FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");
            if(!$this->dbu->move_next())
            {
                $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.ERROR').'<br>';
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
                 $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.UPLOAD').'<br>';
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
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS_IMAGE').'<br>';
            return true;
        }
        else
        {
			$img_path = dirname(dirname(__FILE__)).'/'.$script_path.UPLOAD_PATH.$f_title;
			move_uploaded_file($_FILES['upload_image']['tmp_name'], $img_path);

			$cur_image = ImageCreateFromJPEG($img_path);
			if(imagesy($cur_image)>180)
			  $this->resize($img_path, 0, 180, $f_title);
			  
			$cur_image = ImageCreateFromJPEG($img_path);
			if(imagesx($cur_image)>200)
			  $this->resize($img_path, 200, 0, $f_title);
            
            @chmod($f_out, 0777);
            $this->dbu->query("UPDATE trainer_header_paper SET
                               logo_image='".$f_title."'
                               WHERE trainer_id='".$_SESSION[U_ID]."'" 
                              );
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS_IMAGE').'<br>';
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
	
	function erasecustompicture(&$ld)
	{
        $this->dbu->query("SELECT logo_image FROM trainer_header_paper WHERE trainer_id='".$_SESSION[U_ID]."'");
        if(!$this->dbu->move_next())
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.INVALID_ID')."<br>";
            return false;
        }
        else 
        {
            global $script_path;
            @unlink( $script_path.UPLOAD_PATH.$this->dbu->f('logo_image'));
            $this->dbu->query("UPDATE trainer_header_paper SET logo_image=NULL WHERE trainer_id='".$_SESSION[U_ID]."'");
        }
		$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.IMAGE_DELETED')."<br>";
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
	$_SESSION['userEmail'] = $userEmail = $this->dbu->field("select email from trainer where trainer_id=".$_SESSION[U_ID]);
	
    switch($ld['pay_type']){
        case 'monthly': $is_recurring = $_SESSION['is_recurring'] = true; break;
        case 'per_year': $is_recurring = $_SESSION['is_recurring'] = false; break;
        //case 'yearly': $is_recurring = true; /*some param*/ break;
        default: header("Location: http://rehabmypatient.com/index.php?pag=profile_payment&paym=0"); exit;
    }
	
	//include_once('classes/cls_paypal_new.php');
    include_once('classes/cls_paypal_new_recurring.php');
	paypal_init();
    
	$this->dbu->query("select * from price_plan_new where price_id='".$ld['price_id']."' ");
	$this->dbu->move_next();
    
	$_SESSION['price_id'] = $ld['price_id'];
	$paymentAmount = $_SESSION['Payment_Amount'] = $is_recurring ? round(urlencode($this->dbu->f('price_value'))/12, 2) : urlencode($this->dbu->f('price_value'));
	$currencyCodeType = $this->dbu->f('currency');
	$paymentType = "Sale";
    
	$returnURL = 'http://rehabmypatient.com/index.php?act=member-confirm_pay';
	$cancelURL = 'http://rehabmypatient.com/index.php';
    $NOTIFYURL = 'http://rehabmypatient.com/ipn.php';
    
    if($is_recurring)
        $description = $_SESSION['description'] = 'Monthly payment ('.$paymentAmount.' '.$currencyCodeType.')';
    else
        $description = $_SESSION['description'] = 'Yearly payment';
    
    $resArray = CallShortcutExpressCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $NOTIFYURL, $description, $userEmail, $is_recurring);

	$ack = strtoupper($resArray["ACK"]);
	if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
	{    
		RedirectToPayPal ( $resArray["TOKEN"] );
	} 
	else  
	{
		header("Location: http://rehabmypatient.com/index.php?pag=profile_payment&paym=0");
	}
}

function confirm_pay()
{
    global $glob;
	include_once('classes/cls_paypal_new_recurring.php');
	$token = "";
	if (isset($_REQUEST['token']))
	{
		$token = $_REQUEST['token'];
	}

	if ( $token != "" )
	{
		$resArray = GetShippingDetails( $token );

		$ack = strtoupper($resArray["ACK"]);
		if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
		{
			$this->dbu->query("select * from price_plan_new where price_id='".$_SESSION['price_id']."' ");
			$this->dbu->move_next();
			
			$_SESSION['TOKEN'] = $token;
			$_SESSION['PaymentType'] = 'Sale';
			$_SESSION['payer_id'] = $resArray["PAYERID"];
           
            if($_SESSION['is_recurring']){
                $curTime = time();
                /* parameter reference: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_CreateRecurringPayments */
                $TOKEN = $_SESSION['TOKEN'];
                $PROFILESTARTDATE = date("c", ($curTime + (1 * 24 * 3600)));
                $DESC = $_SESSION['description'];
                $BILLINGPERIOD = 'Month';
                $BILLINGFREQUENCY = 1;
                $TOTALBILLINGCYCLES = '12';
                $AUTOBILLOUTAMT = 'AddToNextBilling';
                $AMT = $_SESSION['Payment_Amount'];
                $CURRENCYCODE = $_SESSION['currencyCodeType'];
                $EMAIL = $_SESSION['userEmail'];
                $L_PAYMENTREQUEST_0_ITEMCATEGORY0 = 'Physical';
                $L_PAYMENTREQUEST_0_NAME0 = 'License';
                $L_PAYMENTREQUEST_0_AMT0 = $_SESSION['Payment_Amount'];
                $L_PAYMENTREQUEST_0_QTY0 = 1;
                //$INITAMT = $_SESSION['Payment_Amount'];
                //$FAILEDINITAMTACTION = 'CancelOnFailure';
                $MAXFAILEDPAYMENTS = 2;

                $resArray = CreateRecurringPaymentsProfile($TOKEN, $PROFILESTARTDATE, $DESC, $BILLINGPERIOD, $BILLINGFREQUENCY, $TOTALBILLINGCYCLES, $AUTOBILLOUTAMT,
                                                           $AMT, $CURRENCYCODE, $EMAIL, $L_PAYMENTREQUEST_0_ITEMCATEGORY0, $L_PAYMENTREQUEST_0_NAME0, $L_PAYMENTREQUEST_0_AMT0,
                                                           $L_PAYMENTREQUEST_0_QTY0, /*$INITAMT,$FAILEDINITAMTACTION,*/ $MAXFAILEDPAYMENTS);
               
                $resArray = GetRecurringPaymentsProfileDetails($resArray['PROFILEID']);
            }
            else
                $resArray = ConfirmPayment($_SESSION['Payment_Amount']);
            
			$ack = strtoupper($resArray["ACK"]);
            if( $ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING" )
            {
                $daysToAdd = 0;
                switch($this->dbu->f('licence_period')){
                    case '1 year':{}
                    default: {$daysToAdd = 365;}
                }
                $expireTime = date("Y-m-d H:i:s", ($curTime + ($daysToAdd * 24 * 3600)));
    
                switch($glob['lang']){
                    case 'us': $dbCountryCode = 'US'; break;
                    default: $dbCountryCode = 'GB';
                }
                $this->dbu->query("SELECT * from `country` WHERE code='".$dbCountryCode."'");
                $this->dbu->move_next();
                $country_id = $this->dbu->f('country_id');

                $this->dbu->query("UPDATE trainer 
                                SET 
                                    paypal_profile_id = '".$payerId."',
                                    country_id 	    = '".$country_id."',
                                    price_plan_id 	= '".$_SESSION['price_id']."',
                                    is_trial		= '0',
                                    expire_date		= '$expireTime'
                                WHERE 
                                    trainer_id=".$_SESSION[U_ID]." ");

                header("Location: http://rehabmypatient.com/index.php?pag=profile&paym=1");
            }
            else{
                header("Location: http://rehabmypatient.com/index.php?pag=profile&paym=0");
            }
		}
		else  
		{
            header("Location: http://rehabmypatient.com/index.php?pag=profile&paym=0");
		}
	
	}

	function confirm_pay()
	{
		include_once('classes/cls_paypal_new.php');
		$token = "";
		if (isset($_REQUEST['token']))
		{
			$token = $_REQUEST['token'];
		}
	
		if ( $token != "" )
		{
			$resArray = GetShippingDetails( $token );
	
			$ack = strtoupper($resArray["ACK"]);
			if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING") 
			{
				/*
				' The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review 
				' page		
				*/
				$token 				= $resArray["TOKEN"];
				$email 				= $resArray["EMAIL"]; // ' Email address of payer.
				$payerId 			= $resArray["PAYERID"]; // ' Unique PayPal customer account identification number.
				$payerStatus		= $resArray["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
				$salutation			= $resArray["SALUTATION"]; // ' Payer's salutation.
				$firstName			= $resArray["FIRSTNAME"]; // ' Payer's first name.
				$middleName			= $resArray["MIDDLENAME"]; // ' Payer's middle name.
				$lastName			= $resArray["LASTNAME"]; // ' Payer's last name.
				$suffix				= $resArray["SUFFIX"]; // ' Payer's suffix.
				$cntryCode			= $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
				$business			= $resArray["BUSINESS"]; // ' Payer's business name.
				$shipToName			= $resArray["SHIPTONAME"]; // ' Person's name associated with this address.
				$shipToStreet		= $resArray["SHIPTOSTREET"]; // ' First street address.
				$shipToStreet2		= $resArray["SHIPTOSTREET2"]; // ' Second street address.
				$shipToCity			= $resArray["SHIPTOCITY"]; // ' Name of city.
				$shipToState		= $resArray["SHIPTOSTATE"]; // ' State or province
				$shipToCntryCode	= $resArray["SHIPTOCOUNTRYCODE"]; // ' Country code. 
				$shipToZip			= $resArray["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
				$addressStatus 		= $resArray["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal   
				$invoiceNumber		= $resArray["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
				$phonNumber			= $resArray["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one.
				
				$this->dbu->query("select * from price_plan_new where price_id='".$_SESSION['price_id']."' ");
				$this->dbu->move_next();
				
				$_SESSION['TOKEN'] = $token;
				$_SESSION['PaymentType'] = 'Sale';
				$_SESSION['currencyCodeType'] = $this->dbu->f('currency');
				$_SESSION['payer_id'] = $payerId;
				
				$resArray = ConfirmPayment($_SESSION['Payment_Amount']);
				$ack = strtoupper($resArray["ACK"]);
				
				$daysToAdd = 0;
				switch($this->dbu->f('licence_period'))
				{
					case 'year':
						{
							$daysToAdd = 365;
							break;
						}
					case 'month':
						{
							$daysToAdd = 30;
							break;
						}
				}
				$curTime = time();
				$expireTime = date("Y-m-d H:i:s", ($curTime + ($daysToAdd * 24 * 3600)));
				
				
				switch($_COOKIE['language']){
					case 'us': $dbCountryCode = 'US'; break;
					default: $dbCountryCode = 'GB';
				}
				$this->dbu->query("SELECT * from `country` WHERE code='".$dbCountryCode."'");
				$this->dbu->move_next();
				$country_id = $this->dbu->f('country_id');
			
				$this->dbu->query("UPDATE trainer 
								SET 
									paypal_profile_id = '".$payerId."',
									country_id 	    = '".$country_id."',
									price_plan_id 	= '".$_SESSION['price_id']."',
									is_trial		= '0',
									expire_date		= '$expireTime'
								WHERE 
									trainer_id=".$_SESSION[U_ID]."
													");
				
				header("Location: /index.php?pag=profile&paym=1");
			} 
			else  
			{
				header("Location: /index.php?pag=profile&paym=0");
				
				//Display a user friendly Error on the page using any of the following error information returned by PayPal
				//$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
				//$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
				//$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
				//$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
				//
				//echo "GetExpressCheckoutDetails API call failed. ";
				//echo "Detailed Error Message: " . $ErrorLongMsg;
				//echo "Short Error Message: " . $ErrorShortMsg;
				//echo "Error Code: " . $ErrorCode;
				//echo "Error Severity Code: " . $ErrorSeverityCode;
			}
		
		}
	}

	/****************************************************************
	* function add_validate(&$ld)                                   *
	****************************************************************/
	function validate_pay(&$ld)
	{
		$is_ok = true;
		if(!$ld['first_name']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_FIRST')."<br>";
		   $is_ok = false;
		}
		if(!$ld['surname']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_SURNAME')."<br>";
		   $is_ok = false;
		}
		if(!$ld['email']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_EMAIL')."<br>";
			$is_ok = false;
		}	
		elseif(!secure_email($ld['email']))
			{
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.INVALID_EMAIL')."<br>";
			
				$is_ok=false;
			}
		if(!$ld['country_id'])
			{
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.SELECT_COUNTRY')."<br>";
		
				$is_ok=false;
			}	
	
	
		if(!$ld['credit_card_type']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.SELECT_CARD_TYPE')."<br>";
		   $is_ok = false;
		}
		if(!$ld['credit_card_no']){
			$ld['error'] .= get_template_tag($ld['pag'], $ld['lang'], 'T.SELECT_CARD_NUM')."<br>";
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
             
                $ld['error'] =get_template_tag($ld['pag'], $ld['lang'], 'T.CANCEL').'<br />';
            return true;
            } else{
                $ld['error'] .= urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);
                return false;
            }	
        }
	}
    function ipn_confirm(&$ld){
        $this->dbu->query("INSERT INTO `paypal_transactions`
                                (`name`)
                         VALUES('Paypal has been here')");
    }
	function delete_profile(&$ld)
		{
			$ld['error']="Not implemented yet.";
		    return true;
		}
	
    function exercise_add(&$ld)
	{
		
        // we'll use some functions from admin lib to avoid code doubling 
        include_once('admin/classes/cls_programs.php');
        $programs = new programs();
		$this->dbu->query("SELECT SUBSTR(programs_code, 4) AS last_code, sort_order FROM programs WHERE programs_code LIKE 'USR%' ORDER BY programs_code DESC ");
		if($this->dbu->move_next())
			$last_code = $this->dbu->f('last_code');
		else
			$last_code = 0;
		
		$last_code++;
		$count_zeros = (3-strlen($last_code) > 0) ? 3-strlen($last_code) : 0;
		$last_code = 'USR'.str_repeat('0', $count_zeros).$last_code;
		
		$ld['pag'] = 'profile_exercise_add';
        if(!$this->validate_exercise_add(&$ld))
        {
            return false;
        }

        $ld['programs_id']=$this->dbu->query_get_id("INSERT INTO programs SET 
																programs_code='".$last_code."', 
																sort_order='".($this->dbu->f('sort_order')+10)."',
																active = 1,
                                                                owner = ".$_SESSION['m_id']);
        foreach(array('en', 'us') as $lang){
            $this->dbu->query("INSERT INTO programs_translate_".$lang." SET 
																programs_id='".$ld['programs_id']."', 
																programs_title='".mysql_real_escape_string($ld['name_'.$lang])."',
																description = '".mysql_real_escape_string($ld['description_'.$lang])."' ");
        }
		$this->dbu->query("INSERT INTO programs_in_category ( programs_id, category_id, main )
                									values ( '".$ld['programs_id']."', '".$ld['subcategory']."', '1' ) ");
        
        if($programs->image_validate()){
            $programs->upload_file($ld);
			$ld['pag'] = 'programs_user';
			$ld['error'] = 'Exercise added.';
			return true;
        }
        else {
			$ld['error'] = 'Some error.';
            return false;
        }
    }
	
    function validate_exercise_add(&$ld)
	{
		$is_ok=true;

		if(!$ld['name_en'] || !$ld['name_us'])
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_NAME')."<br>";
            $is_ok=false;
        }
		if(!$ld['description_en'] || !$ld['description_us'])
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_DESCR')."<br>";
            $is_ok=false;
        }
		if($ld['category'] == -1)
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_CAT')."<br>";
            $is_ok=false;
        }
		if($ld['subcategory'] == -1)
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_SUBCAT')."<br>";
            $is_ok=false;
        }
        if(!$_FILES['image']['name'] || !$_FILES['lineart']['name'])
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_IMAGE')."<br>";
            $is_ok=false;
        }
		return $is_ok;
	}
	
	function exercise_update(&$ld)
	{
        // we'll use some functions from admin lib to avoid code doubling 
        include_once('admin/classes/cls_programs.php');
        $programs = new programs();
		
        if(!$this->validate_exercise_update(&$ld))
        {
            $ld['pag'] = 'profile_exercise_update';
            return false;
        }

        foreach(array('en', 'us') as $lang){
            $this->dbu->query("UPDATE programs_translate_".$lang." SET 
								   programs_title='".mysql_real_escape_string($ld['name_'.$lang])."',
								   description = '".mysql_real_escape_string($ld['description_'.$lang])."'
								WHERE programs_id='".$ld['programs_id']."'
							");
        }
		
		$this->dbu->query("DELETE FROM programs_in_category WHERE programs_id = '".$ld['programs_id']."' ");
		$this->dbu->query("INSERT INTO programs_in_category ( programs_id, category_id, main )
                									values ( '".$ld['programs_id']."', '".$ld['subcategory']."', '1' ) ");
        
        if($programs->image_validate()){
            $programs->upload_file($ld);
			$ld['error'] = 'Exercise updated.';
			return true;
        }
        else {
            return false;
        }
		
		return true;
    }
	
	function validate_exercise_update(&$ld)
	{
		$is_ok=true;

		if(!$ld['name_en'] || !$ld['name_us'])
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_NAME')."<br>";
            $is_ok=false;
        }
		if(!$ld['description_en'] || !$ld['description_us'])
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_DESCR')."<br>";
            $is_ok=false;
        }
		if($ld['category'] == -1)
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_CAT')."<br>";
            $is_ok=false;
        }
		if($ld['subcategory'] == -1)
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_SUBCAT')."<br>";
            $is_ok=false;
        }
		return $is_ok;
	}
	
	function exercise_delete(&$ld)
	{
		if(!$this->validate_exercise_delete(&$ld))
        {
            $ld['pag'] = 'programs_user';
            return false;
        }
		include_once('admin/classes/cls_programs.php');
        $programs = new programs();
		
		$programs->erasepicture($ld);
		
		$this->dbu->query("DELETE FROM programs WHERE programs_id='".$ld['programs_id']."'");
        foreach(array('en', 'us') as $lang)
			$this->dbu->query("DELETE FROM programs_translate_".$lang." WHERE programs_id='".$ld['programs_id']."'");

		$this->dbu->query("DELETE FROM programs_in_category WHERE programs_id='".$ld['programs_id']."'");
		
		//delete program id
		$query_string = "SELECT * FROM exercise_plan WHERE (exercise_program_id LIKE '%,".$ld['programs_id']."%' OR exercise_program_id LIKE '%,".$ld['programs_id'].",%' OR exercise_program_id LIKE '%".$ld['programs_id']."%' OR exercise_program_id LIKE '%".$ld['programs_id'].",%') AND trainer_id = ".$_SESSION[U_ID];
		$this->dbu->query($query_string);
		while($this->dbu->move_next())
		{
			$replace_str = $this->dbu->f('exercise_program_id');
			$replace_str = str_replace(','.$ld['programs_id'].',', ',', $replace_str);
			$replace_str = str_replace(array($ld['programs_id'].',', ','.$ld['programs_id'], $ld['programs_id']), '', $replace_str);
			
			$query_string = "UPDATE exercise_plan SET exercise_program_id='$replace_str' WHERE exercise_plan_id=".$this->dbu->f('exercise_plan_id')." AND trainer_id = ".$_SESSION[U_ID];
			$this->dbu->query($query_string);
		}
		
		$query_string = "SELECT * FROM exercise_program_plan WHERE (exercise_program_id LIKE '%,".$ld['programs_id']."%' OR exercise_program_id LIKE '%,".$ld['programs_id'].",%' OR exercise_program_id LIKE '%".$ld['programs_id']."%' OR exercise_program_id LIKE '%".$ld['programs_id'].",%') AND trainer_id = ".$_SESSION[U_ID];
		$this->dbu->query($query_string);
		while($this->dbu->move_next())
		{
			$replace_str = $this->dbu->f('exercise_program_id');
			$replace_str = str_replace(','.$ld['programs_id'].',', ',', $replace_str);
			$replace_str = str_replace(array($ld['programs_id'].',', ','.$ld['programs_id'], $ld['programs_id']), '', $replace_str);
			
			$query_string = "UPDATE exercise_program_plan SET exercise_program_id='$replace_str' WHERE exercise_program_plan_id=".$this->dbu->f('exercise_program_plan_id')." AND trainer_id = ".$_SESSION[U_ID];
			$this->dbu->query($query_string);
		}
		
		$query_string = "DELETE FROM exercise_plan_set WHERE exercise_program_id = '".$ld['programs_id']."' AND trainer_id = ".$_SESSION[U_ID];
		$this->dbu->query($query_string);
		
		return true;
	}
	
	function validate_exercise_delete(&$ld)
	{
		$is_ok=true;

		if(!$ld['programs_id'] || !$ld['programs_id'])
        {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.EMPTY_ID')."<br>";
            $is_ok=false;
        }
		return $is_ok;
	}
}//end class