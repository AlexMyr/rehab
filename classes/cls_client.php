<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
class client
{
	var $dbu;

	function client()
		{
			$this->dbu = new mysql_db();
		}
		
	/* Client SECTION */

	/****************************************************************
	* function add_client(&$ld)                                     *
	****************************************************************/

	function add_client(&$ld)
		{
			if(!$this->validate_add_client($ld))
				{
					return false;
				}
		global $user_level;
	    
	    $this->dbu->query("
							SELECT 
								client.client_id
							FROM 
								client 
							WHERE 
								1=1 
								AND trainer_id = ".$_SESSION[U_ID]." 
								AND first_name = '".$ld['first_name']."'
								AND surname = '".$ld['surname']."'
								AND email = '".$ld['email']."'
								AND print_image_type = '".$ld['print_image_type']."'
						");
	    /* CHECK IF Client EXIST IN DB, IF NOT, SAVE IT IN DB */
		if($this->dbu->move_next())
			{
				$ld['error'] = 'Client already exist!';
				return true;
			}
		else 
			{
//			$this->dbu->query("
			$ld['client_id']=$this->dbu->query_get_id("
								INSERT INTO 
											client 
								SET 
											first_name='".$ld['first_name']."', 
											surname='".$ld['surname']."', 
											email='".$ld['email']."', 
											print_image_type='".$ld['print_image_type']."', 
											client_note='".$ld['client_note']."', 
											create_date=NOW(),
											modify_date=NOW(),
											trainer_id = ".$_SESSION[U_ID]." 
								");
			$ld['first_name']=''; 
			$ld['surname']='';
			$ld['email']=''; 
			$ld['print_image_type']='';
			$ld['client_note']='';
			
			$ld['error']="Client Succesfully added.";
			
		    return true;
			}
		}
		
	function validate_add_client(&$ld)
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
				
			//if(!$ld['email'])
			//	{
			//		$ld['error'].="Please fill in the 'Email' field."."<br>";
			//		$is_ok=false;
			//	}
			if($ld['email'] && !secure_email($ld['email']))
				{
					$ld['error'].="Please provide a valid email address."."<br>";
					$is_ok=false;
				}

/*				
			if(!$ld['client_note'])
				{
					$ld['error'].="Please fill in the 'website' field."."<br>";
					$is_ok=false;
				}
*/
			return $is_ok;
		}

	function add_program_plan(&$ld)
	{
		if(!$this->validate_add_program_plan($ld))
		{
			return false;
		}
		global $user_level;
	
		$this->dbu->query("
							SELECT 
								exercise_program_plan_id
							FROM 
								exercise_program_plan 
							WHERE 
								1=1 
								AND trainer_id = ".$_SESSION[U_ID]." 
								AND program_name = '".$ld['program_name']."'
								AND print_image_type = '".$ld['print_image_type']."'
						");
		
		/* CHECK IF Programme EXIST IN DB, IF NOT, SAVE IT IN DB */
		if($this->dbu->move_next())
		{
			$ld['error'] = 'Programme already exist!';
			return true;
		}
		else 
		{

			$ld['client_id']=$this->dbu->query_get_id("
							INSERT INTO 
										exercise_program_plan 
							SET 
										program_name='".mysql_escape_string($ld['program_name'])."', 
										print_image_type='".$ld['print_image_type']."', 
										client_note='".$ld['client_note']."', 
										date_created=NOW(),
										date_modified=NOW(),
										trainer_id = ".$_SESSION[U_ID]." 
							");
			$ld['program_name']=''; 
			$ld['print_image_type']='';
			$ld['client_note']='';
		
			$ld['error']="Programme Succesfully added.";
		
			return true;
		}
	}
	
	function delete_program_plan(&$ld)
	{
		$this->dbu->query("DELETE FROM exercise_program_plan
							WHERE
								trainer_id = '".$_SESSION[U_ID]."' AND
								exercise_program_plan_id = '".$ld['program_id']."' 
							");
		$ld['error'] = "Programme deleted successfully.";
		return true;
	}
	
	function update_program_exercise(&$ld)
	{
		$ld['exercise_id'] = rtrim($ld['exercise_id'],',');
		$this->dbu->query("
							UPDATE 
								exercise_program_plan 
							SET 
								exercise_program_id='".$ld['exercise_id']."',
								date_modified=NOW()
							WHERE
								exercise_program_plan_id='".$ld['program_id']."'
							");
		
		$del_id = explode(',',$ld['exercise_id']);
		$get_exercises = $this->dbu->query("SELECT exercise_plan_set.* FROM exercise_plan_set WHERE 1=1 AND exercise_plan_id='".$ld['program_id']."' AND client_id= ".$ld['program_id']." AND is_program_plan=1 ");
		while($get_exercises->next())
		{
			if(!in_array($get_exercises->gf('exercise_program_id'),$del_id))
				$this->dbu->query("DELETE FROM exercise_plan_set WHERE exercise_program_id=".$get_exercises->gf('exercise_program_id')." AND exercise_plan_id='".$ld['program_id']."' AND client_id= ".$ld['program_id']." AND is_program_plan=1 ");
		}
					
		//$this->dbu->query("
		//					UPDATE
		//								client 
		//					SET 
		//								modify_date=NOW()
		//					WHERE 
		//								client_id = ".$ld['client_id']."
		//						AND
		//								trainer_id = ".$_SESSION[U_ID]." 
		//					");
		
		return true;			
	}
	
	function update_program_exercise_plan(&$ld)
	{
		$exercise = explode(',',$ld['exercise_id']);
		$i=0;
		while ($i<count($exercise)) 
		{
			$this->dbu->query("SELECT * FROM exercise_plan_set WHERE 
								exercise_plan_id = '".$ld['program_id']."' AND
								exercise_program_id = '".$exercise[$i]."' AND
								trainer_id = '".$_SESSION[U_ID]."' AND
								client_id = '".$ld['program_id']."' AND
								is_program_plan = 1
								");
			
			if($this->dbu->move_next())
			{
				$this->dbu->query("UPDATE exercise_plan_set 
					SET 
						plan_description = '".mysql_escape_string($ld['description'.$exercise[$i]])."',
						plan_set_no = '".mysql_escape_string($ld['sets'.$exercise[$i]])."',
						plan_repetitions = '".mysql_escape_string($ld['repetitions'.$exercise[$i]])."',
						plan_time = '".mysql_escape_string($ld['time'.$exercise[$i]])."'
					WHERE
						exercise_plan_id = '".$ld['program_id']."' AND
						exercise_program_id = '".$exercise[$i]."' AND
						trainer_id = '".$_SESSION[U_ID]."' AND
						client_id = '".$ld['program_id']."' 
					");
			}
			else
			{		
				$this->dbu->query("
					 INSERT INTO
						exercise_plan_set 
					 SET
						exercise_plan_id = '".$ld['program_id']."',
						exercise_program_id = '".$exercise[$i]."',
						plan_description = '".mysql_escape_string($ld['description'.$exercise[$i]])."',
						plan_set_no = '".mysql_escape_string($ld['sets'.$exercise[$i]])."',
						plan_repetitions = '".mysql_escape_string($ld['repetitions'.$exercise[$i]])."',
						plan_time = '".mysql_escape_string($ld['time'.$exercise[$i]])."',
						trainer_id = '".$_SESSION[U_ID]."',
						client_id = '".$ld['program_id']."',
						is_program_plan = 1
					");			
			}
			$i++;			
		}
		
		$this->dbu->query("UPDATE exercise_program_plan 
							SET 
								exercise_notes = '".$ld['exercise_notes']."',
								date_modified = NOW()
							WHERE
								trainer_id = '".$_SESSION[U_ID]."' AND
								exercise_program_plan_id = '".$ld['program_id']."' 
							");
		
		return true;	
	}
	
	function send_program_email(&$ld)
	{
		//check is program has exercise
		$exerciseString = $this->dbu->field("SELECT exercise_program_id
							FROM 
								exercise_program_plan 
							WHERE 
								1=1
							AND
								exercise_program_plan_id=".$ld['program_id']." ");
		
		if(!$exerciseString)
		{
			$ld['error'] = 'This programme is empty. Please add some exercises before emailing.';
			return false;
		}
		
		if(isset($ld['print']))
		{
			header("Location: index.php?pag=pexercisepdf&program_id=".$ld['program_id']."&first_name=".$ld['first_name']."&surname=".$ld['surname']." ");
			exit;
		}

		if(!$this->validate_send_program_email($ld))
		{
			return false;
		}
		
		$this->dbu->query("SELECT trainer_header_paper.company_name, trainer_header_paper.email AS trainer_email, trainer_header_paper.website, trainer_header_paper.phone
							FROM 
								trainer_header_paper 
							WHERE 
								trainer_id = '".$_SESSION[U_ID]."' 
						 ");
		if($this->dbu->move_next())
		{
			$this->generate_pdf_for_program($ld);
			$message_data=get_sys_message('sendpdf');
			$ordermail = $ld['email'];
			$fromMail = $this->dbu->gf('trainer_email'); 
			$replyMail = $message_data['from_email'];
	
			$body=$message_data['text'];
			
			//$body=str_replace('[!NAME!]',$this->dbu->f('first_name')." ".$this->dbu->f('last_name'), $body );
			$body=str_replace('[!FIRSTNAME!]',$ld['first_name'], $body );
			$body=str_replace('[!SURNAME!]',$ld['surname'], $body );
			$body=str_replace('[!COMPANYNAME!]',$this->dbu->f('company_name'), $body );
			$body=str_replace('[!CLINICNUMBER!]',$this->dbu->f('phone'), $body );
			$body=str_replace('[!CLINICWEBSITE!]',$this->dbu->f('website'), $body );
                
	        require_once ('class.phpmailer.php');        
	        include_once ("classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
			
			$mail = new PHPMailer();
			$mail->IsSMTP(); // telling the class to use SMTP
	        $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
	        $mail->SMTPAuth = true; // enable SMTP authentication
	        $mail->Host = SMTP_HOST; // sets the SMTP server
	        $mail->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
	        $mail->Username = SMTP_USERNAME; // SMTP account username
	        $mail->Password = SMTP_PASSWORD; // SMTP account password
	        $mail->SetFrom($fromMail, $fromMail);
	        $mail->Subject = $message_data['subject'];
			$mail->AddAttachment("pdf/exercisepdf.pdf", 'exercise_'.$ld['program_id'].'.pdf'); // attach files/invoice-user-1234.pdf, and rename it to invoice.pdf
	        $mail->MsgHTML($body);
			$mail->AddAddress($ordermail, $ld['first_name']." ".$ld['surname']);
        
	        $mail->Send();
		
			$ld['error']="Programme sent successfully to patient email box.";
			return true;					
		}
		else
		{
			$ld['error']="Error..";
	        return false;								
		}
	}
	
	function generate_pdf_for_program(&$ld)
	{
		include_once('php/pexercisepdf.php');		
	}
		
	function validate_add_program_plan(&$ld)
	{
		$is_ok=true;
		if(!$ld['program_name'])
		{
			$ld['error'].="Please fill in the 'Name' field."."<br>";
			$is_ok=false;
		}
		return $is_ok;
	}
	
	function validate_send_program_email(&$ld)
	{
		$is_ok=true;
		if(!$ld['email'])
		{
			$ld['error'].="Please fill in the 'Email' field."."<br>";
			$is_ok=false;
		}
		return $is_ok;
	}

	/****************************************************************
	* function update_client(&$ld)                                  *
	****************************************************************/

	function update_client(&$ld)
	{
		if(!$this->validate_update_client($ld))
		{
			return false;
		}
		global $user_level;
	
		$this->dbu->query("
							UPDATE
										client 
							SET 
										first_name='".$ld['first_name']."', 
										surname='".$ld['surname']."', 
										email='".$ld['email']."', 
										print_image_type='".$ld['print_image_type']."', 
										client_note='".$ld['client_note']."', 
										modify_date=NOW()
							WHERE 
										client_id = ".$ld['client_id']."
								AND
										trainer_id = ".$_SESSION[U_ID]." 
							");
	
		$ld['error']="Client Succesfully updated.";

		return true;
	}
		
	function validate_update_client(&$ld)
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
		//if(!$ld['email'])
		//	{
		//		$ld['error'].="Please fill in the 'Email' field."."<br>";
		//		$is_ok=false;
		//	}
		if($ld['email'] && !secure_email($ld['email']))
		{
			$ld['error'].="Please provide a valid email address."."<br>";
			$is_ok=false;
		}
/*				
*/
		return $is_ok;
	}
		
	/****************************************************************
	* function delete_client(&$ld)                                  *
	****************************************************************/
	
	function delete_client(&$ld)
	{
		if(!$this->delete_client_validate($ld))
		{
			return false;
		}
		
		$this->dbu->query("DELETE FROM exercise_plan_set WHERE client_id='".$ld['client_id']."'");
		$this->dbu->query("DELETE FROM exercise_plan WHERE client_id='".$ld['client_id']."'");
		$this->dbu->query("DELETE FROM client WHERE client_id='".$ld['client_id']."'");
	    
		$ld['error']="Client Succesfully deleted.";
	
	    return true;
	}
	
	function delete_client_validate(&$ld)
	{
		$is_ok = true;
		
		return $is_ok;
	}
		
	/* Exercise SECTION */
	
	function add_exercise(&$ld)
		{
//			$ld['error']="Add exercise not implemented yet.";
//			return true;			
//			return false;
		    $ld['exercise_id'] = rtrim($ld['exercise_id'],',');
			$ld['exercise_plan_id']=$this->dbu->query_get_id("
								INSERT INTO 
											exercise_plan 
								SET 
											exercise_program_id='".$ld['exercise_id']."', 
											date_created=NOW(), 
											date_modified=NOW(), 
											trainer_id='".$_SESSION[U_ID]."', 
											client_id= ".$ld['client_id']." 
								");
		
			$this->dbu->query("
								UPDATE
											client 
								SET 
											modify_date=NOW()
								WHERE 
											client_id = ".$ld['client_id']."
									AND
											trainer_id = ".$_SESSION[U_ID]." 
								");
			
			$ld['error']="Exercise Program Succesfully added to Client.";

		    return true;
		}

	function update_exercise(&$ld)
		{
			$ld['exercise_id'] = rtrim($ld['exercise_id'],',');
			$this->dbu->query("
								UPDATE 
											exercise_plan 
								SET 
											exercise_program_id='".$ld['exercise_id']."',
											date_modified=NOW(), 
											trainer_id='".$_SESSION[U_ID]."', 
											client_id= ".$ld['client_id']." 
								WHERE
											exercise_plan_id='".$ld['exercise_plan_id']."'
								");
			$del_id = explode(',',$ld['exercise_id']);
			$get_exercises = $this->dbu->query("SELECT exercise_plan_set.* FROM exercise_plan_set WHERE 1=1 AND exercise_plan_id='".$ld['exercise_plan_id']."' AND client_id= ".$ld['client_id']." ");
			while($get_exercises->next())
			{
				if(!in_array($get_exercises->gf('exercise_program_id'),$del_id))
				 	$this->dbu->query("DELETE FROM exercise_plan_set WHERE exercise_program_id=".$get_exercises->gf('exercise_program_id')." AND exercise_plan_id='".$ld['exercise_plan_id']."' AND client_id= ".$ld['client_id']." ");
			}
						
			$this->dbu->query("
								UPDATE
											client 
								SET 
											modify_date=NOW()
								WHERE 
											client_id = ".$ld['client_id']."
									AND
											trainer_id = ".$_SESSION[U_ID]." 
								");
			
//			$ld['error']="Update exercise not implemented yet.";
			return true;			
//			return false;
		}
	
	function update_exercise_plan(&$ld)
	{
		if(!$this->validate_update_exercise_plan($ld))
		{
			return false;
		}
		$exercise = explode(',',$ld['exercise_id']);
		
		$i=0;
		while ($i<count($exercise)) 
		{
			$this->dbu->query("SELECT * FROM exercise_plan_set WHERE 
					 exercise_plan_id = '".$ld['exercise_plan_id']."' AND
					 exercise_program_id = '".$exercise[$i]."' AND
					 trainer_id = '".$_SESSION[U_ID]."' AND
					 client_id = '".$ld['client_id']."' AND
					 is_program_plan = 0
					 ");
			
			if($this->dbu->move_next())
			{
				$this->dbu->query("UPDATE exercise_plan_set 
					SET 
						plan_description = '".mysql_escape_string($ld['description'.$exercise[$i]])."',
						plan_set_no = '".mysql_escape_string($ld['sets'.$exercise[$i]])."',
						plan_repetitions = '".mysql_escape_string($ld['repetitions'.$exercise[$i]])."',
						plan_time = '".mysql_escape_string($ld['time'.$exercise[$i]])."'
					WHERE
						exercise_plan_id = '".$ld['exercise_plan_id']."' AND
						exercise_program_id = '".$exercise[$i]."' AND
						trainer_id = '".$_SESSION[U_ID]."' AND
						client_id = '".$ld['client_id']."' 
					");
			}
			else
			{		
				$this->dbu->query("
					 INSERT INTO
						 exercise_plan_set 
					 SET
						 exercise_plan_id = '".$ld['exercise_plan_id']."',
						 exercise_program_id = '".$exercise[$i]."',
						 plan_description = '".mysql_escape_string($ld['description'.$exercise[$i]])."',
						 plan_set_no = '".mysql_escape_string($ld['sets'.$exercise[$i]])."',
						 plan_repetitions = '".mysql_escape_string($ld['repetitions'.$exercise[$i]])."',
						 plan_time = '".mysql_escape_string($ld['time'.$exercise[$i]])."',
						 trainer_id = '".$_SESSION[U_ID]."',
						 client_id = '".$ld['client_id']."',
						 is_program_plan = 0
					");			
			}
			$i++;			
		}
		
		$this->dbu->query("UPDATE exercise_plan 
			SET 
				exercise_notes = '".mysql_escape_string($ld['exercise_notes'])."'
			WHERE
				exercise_plan_id = '".$ld['exercise_plan_id']."' AND
				trainer_id = '".$_SESSION[U_ID]."' AND
				client_id = '".$ld['client_id']."' 
			");
		
		$this->dbu->query("UPDATE client SET modify_date=NOW() WHERE client_id=".$ld['client_id']." AND
											trainer_id = ".$_SESSION[U_ID]." ");
		//$ld['pag'] = 'pagina de pdf';
		return true;	
	}
	
	function validate_update_exercise_plan(&$ld)
		{
			$is_ok = true;
/*			
			$exercise = explode(',',$ld['exercise_id']);
			$i=0;
			while($i<count($exercise)-1) 
				{
					if(!$ld['sets'.$exercise[$i]])
						{
							$ld['error'] = "Please fill all the fields";
							$is_ok = false;
						}
					if(!$ld['repetitions'.$exercise[$i]])
						{
							$ld['error'] = "Please fill all the fields";
							$is_ok = false;
						}
					if(!$ld['time'.$exercise[$i]])
						{
							$ld['error'] = "Please fill all the fields";
							$is_ok = false;
						}
					$i++;			
				}
*/				
			return $is_ok;		
		}
		
	function delete_exercise(&$ld)
		{
			$this->dbu->query("DELETE 
									FROM 
										exercise_plan 
									WHERE 
											client_id = ".$ld['client_id']."
										AND
											trainer_id = ".$_SESSION[U_ID]." 
										AND
											exercise_plan_id = ".$ld['exercise_plan_id']."");

			$this->dbu->query("
								UPDATE
											client 
								SET 
											modify_date=NOW()
								WHERE 
											client_id = ".$ld['client_id']."
									AND
											trainer_id = ".$_SESSION[U_ID]." 
								");

			$ld['error']="Exercise Succesfully deleted.";
			return true;			
		}

	function generate_pdf(&$ld)
		{
			include_once('php/exercisepdf.php');		
		}
		
	function mail_exercise(&$ld)
		{
			
				//$this->dbu->query("SELECT client.*, trainer_profile.company_name, trainer_profile.email AS trainer_email, trainer_profile.website, trainer_profile.phone
				//					FROM 
				//						client 
				//					INNER JOIN 
				//						trainer_profile 
				//							ON 
				//								client.trainer_id=trainer_profile.trainer_id
				//					WHERE 
				//						client.trainer_id = '".$_SESSION[U_ID]."' 
				//					AND
				//						client_id = '".$ld['client_id']."' 
				//		 ");
				
				$this->dbu->query("SELECT client.*, trainer_header_paper.company_name, trainer_header_paper.email AS trainer_email, trainer_header_paper.website, trainer_header_paper.phone
									FROM 
										client 
									INNER JOIN 
										trainer_header_paper 
											ON 
												client.trainer_id=trainer_header_paper.trainer_id
									WHERE 
										client.trainer_id = '".$_SESSION[U_ID]."' 
									AND
										client_id = '".$ld['client_id']."' 
						 ");
				if($this->dbu->move_next())
				{
				//echo "veva";
        		//die();	
//        global $site_url, $contact_thankyou_page_id;
//        $ordermail = 'claudiu@medeeaweb.com';
		
		$this->generate_pdf($ld);
		$message_data=get_sys_message('sendpdf');
        $ordermail = $this->dbu->gf('email');
        $fromMail = $this->dbu->gf('trainer_email'); 
        $replyMail = $message_data['from_email'];

		$body=$message_data['text'];

		$body=str_replace('[!NAME!]',$this->dbu->f('first_name')." ".$this->dbu->f('last_name'), $body );
		$body=str_replace('[!FIRSTNAME!]',$this->dbu->f('first_name'), $body );
		$body=str_replace('[!SURNAME!]',$this->dbu->f('last_name'), $body );
		$body=str_replace('[!COMPANYNAME!]',$this->dbu->f('company_name'), $body );
		$body=str_replace('[!CLINICNUMBER!]',$this->dbu->f('phone'), $body );
		$body=str_replace('[!CLINICWEBSITE!]',$this->dbu->f('website'), $body );
                
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
/*
        $mail->SetFrom(ORDER_EMAIL, ORDER_EMAIL);
        $mail->AddReplyTo(ORDER_EMAIL, ORDER_EMAIL);
*/
        $mail->SetFrom($fromMail, $fromMail);
        //$mail->AddReplyTo($replyMail, $replyMail);
        $mail->Subject = $message_data['subject'];
        
        //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
         
        $mail->AddAttachment("pdf/exercisepdf.pdf", 'exercise_'.$ld['exercise_plan_id'].'.pdf'); // attach files/invoice-user-1234.pdf, and rename it to invoice.pdf
        $mail->MsgHTML($body);
        
        //$address = $receiver_email;
        $mail->AddAddress($ordermail, $this->dbu->gf('first_name')." ".$this->dbu->gf('surname'));
        
        $mail->Send();
			$ld['error']="Exercise sent successfully to client email box.";
        return true;					
				}
		else
		{
			$ld['error']="Please fill in your profile on My account page.";
        return false;								
		}

//			return true;			
//			return false;
		}
		
		
		
	/* NOT IMPLEMENTED YET */

	function print_exercise(&$ld)
		{
			$ld['error']="Print exercise not implemented yet.";
//			return true;			
			return false;
		}
		
	function pdf_exercise(&$ld)
		{
			$ld['error']="PDF exercise not implemented yet.";
//			return true;			
			return false;
		}
		
}//end class