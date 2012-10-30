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
			$this->dbu2 = new mysql_db();
			$this->dbu3 = new mysql_db();
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
	    
	//    $this->dbu->query("
	//						SELECT 
	//							client.client_id
	//						FROM 
	//							client 
	//						WHERE 
	//							1=1 
	//							AND trainer_id = ".$_SESSION[U_ID]." 
	//							AND first_name = '".mysql_real_escape_string($ld['first_name'])."'
	//							AND surname = '".mysql_real_escape_string($ld['surname'])."'
	//							AND email = '".mysql_real_escape_string($ld['email'])."'
	//							AND print_image_type = '".mysql_real_escape_string($ld['print_image_type'])."'
	//					");
	    
		$this->dbu->query("
							SELECT 
								client.client_id
							FROM 
								client 
							WHERE 
								1=1 
								AND trainer_id = ".$_SESSION[U_ID]." 
								AND email = '".mysql_real_escape_string($ld['email'])."'
						");
		/* CHECK IF Client EXIST IN DB, IF NOT, SAVE IT IN DB */
		if(false) //$this->dbu->move_next())
		{
            $ld['error'] = get_template_tag($ld['pag'], $ld['lang'], 'T.EXIST');
            return false;
		}
		else 
		{
			$ld['client_id']=$this->dbu->query_get_id("
								INSERT INTO 
											client 
								SET 
											first_name='".mysql_escape_string($ld['first_name'])."', 
											surname='".mysql_escape_string($ld['surname'])."',
											appeal='".mysql_escape_string($ld['appeal'])."',
											email='".mysql_escape_string($ld['email'])."', 
											print_image_type='".mysql_escape_string($ld['print_image_type'])."', 
											client_note='".mysql_escape_string($ld['client_note'])."', 
											create_date=NOW(),
											modify_date=NOW(),
											trainer_id = ".$_SESSION[U_ID]." 
								");
			$ld['first_name']=''; 
			$ld['surname']='';
			$ld['email']=''; 
			$ld['print_image_type']='';
			$ld['client_note']='';
			
			$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS');
			header("location: /index.php?pag=client&client_id=".$ld['client_id']."&success=true&error=".$ld['error']);exit;
		    return true;
		}
	}
		
	function validate_add_client(&$ld)
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
		if($ld['email'] && !secure_email($ld['email']))
		{
			$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.VALID_EMAIL')."<br>";
			$is_ok=false;
		}
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
								AND program_name = '".mysql_real_escape_string($ld['program_name'])."'
								AND print_image_type = '".mysql_real_escape_string($ld['print_image_type'])."'
						");
		

		/* CHECK IF Programme EXIST IN DB, IF NOT, SAVE IT IN DB */
		if($this->dbu->move_next())
		{
			$ld['error'] = get_template_tag('dashboard', $ld['lang'], 'T.PROG_EXIST');
			return false;
		}
		else 
		{

			$ld['client_id']=$this->dbu->query_get_id("
							INSERT INTO 
										exercise_program_plan 
							SET 
										program_name='".mysql_escape_string($ld['program_name'])."', 
										print_image_type='".mysql_real_escape_string($ld['print_image_type'])."', 
										client_note='".mysql_real_escape_string($ld['exercise_note'])."', 
										date_created=NOW(),
										date_modified=NOW(),
										trainer_id = ".$_SESSION[U_ID]." 
							");
			$ld['program_name']=''; 
			$ld['print_image_type']='';
			$ld['exercise_note']='';
		
			$ld['error']= get_template_tag('dashboard', $ld['lang'], 'T.PROG_SUCCESS');
		
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
		$ld['error'] = get_template_tag($ld['pag'], $ld['lang'], 'T.PROG_DELETE');
		return true;
	}
	
	function update_program_exercise(&$ld)
	{
		$ld['exercise_id'] = rtrim($ld['exercise_id'],',');
        
		$default_program_desc = get_template_tag('program_update_exercise', $ld['lang'], 'T.PROGRAM_DESC_DEFAULT');

		if($ld['program_desc'] == $default_program_desc)
			$ld['program_desc'] = '';
		
		$this->dbu->query("
							UPDATE 
								exercise_program_plan 
							SET 
								exercise_program_id='".$ld['exercise_id']."',
                                client_note = '".mysql_real_escape_string($ld['program_desc'])."',
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
        
		return true;			
	}
    
	function update_custom_description($ld){
        if( isset($ld['ex_id']) && isset($ld['descr']) && isset($ld['program_id']) ){
            $this->dbu->query('SELECT * FROM `programs_custom_descr` WHERE program_id = '.$ld['program_id'].' AND exercise_id ='.$ld['ex_id']);
            $this->dbu->move_next();
            if($this->dbu->f('exercise_id')){
                $this->dbu->query('UPDATE `programs_custom_descr`
                                    SET description = "'.mysql_real_escape_string($ld['descr']).'"
                                    WHERE exercise_id = "'.$ld['ex_id'].'" AND program_id = "'.$ld['program_id'].'"');
            }
            else{
                $this->dbu->query('INSERT INTO `programs_custom_descr` (exercise_id, program_id, description)
                                    VALUES ('.$ld['ex_id'].', '.$ld['program_id'].', "'.mysql_real_escape_string($ld['descr']).'");');
            }

			$this->dbu->query('SELECT * FROM `exercise_plan_set` WHERE exercise_program_id = '.$ld['ex_id'].' AND exercise_plan_id ='.$ld['program_id']);
			$this->dbu->move_next();
			if($this->dbu->f('exercise_program_id')){
                $this->dbu->query('UPDATE `exercise_plan_set`
                                    SET plan_description = "'.mysql_real_escape_string($ld['descr']).'"
                                    WHERE exercise_program_id = "'.$ld['ex_id'].'" AND exercise_plan_id = "'.$ld['program_id'].'"');
            }
        }
        exit;
    }
    
	function update_program_exercise_plan(&$ld)
	{
		$is_custom = $this->dbu->field("select parent_plan from exercise_program_plan where trainer_id=".$_SESSION[U_ID]." and exercise_program_plan_id=".$ld['program_id']) ? true : false;
		
		//update secondary
		$this->dbu->query("select exercise_program_plan_id, client_id from exercise_program_plan where trainer_id=".$_SESSION[U_ID]." and parent_plan=".$ld['program_id']);
		while($this->dbu->move_next())
		{
			$secondary_program[] = array('client_id'=> $this->dbu->f('client_id'), 'program_id'=>$this->dbu->f('exercise_program_plan_id'));
		}
//var_dump($secondary_program);exit;
		if(count($secondary_program) && !$is_custom)
		{
			$this->dbu->query("select * from exercise_program_plan where exercise_program_plan_id=".$ld['program_id']);
			$this->dbu->move_next();
			$program_name = $this->dbu->f('program_name');
			$exercise_program_id = $this->dbu->f('exercise_program_id');
			$exercise_notes = $this->dbu->f('exercise_notes');
			$print_image_type = $this->dbu->f('print_image_type');
			$client_note = $this->dbu->f('client_note');
			
			for($i=0; $i<count($secondary_program);$i++)
			{
				$this->dbu->query("update exercise_program_plan
									set program_name='".$program_name."',
										exercise_program_id='".$exercise_program_id."',
										exercise_notes='".$exercise_notes."',
										print_image_type='".$print_image_type."',
										client_note='".$client_note."'
								  where exercise_program_plan_id=".$secondary_program[$i]['program_id']);
				
				$this->dbu->query("delete from exercise_plan_set where exercise_plan_id=".$secondary_program[$i]['program_id']);
				$this->dbu->query("delete from programs_custom_descr where program_id=".$secondary_program[$i]['program_id']);
			}
		}
		
		$exercise = explode(',',$ld['exercise_id']);
		$i=0;
		while ($i<count($exercise)) 
		{
			
			$this->dbu->query('SELECT * FROM `programs_custom_descr` WHERE program_id = '.$ld['program_id'].' AND exercise_id ='.$exercise[$i]);
            $this->dbu->move_next();
            if($this->dbu->f('exercise_id')){
                $this->dbu->query('UPDATE `programs_custom_descr`
                                    SET description = "'.mysql_real_escape_string($ld['description'.$exercise[$i]]).'"
                                    WHERE exercise_id = "'.$exercise[$i].'" AND program_id = "'.$ld['program_id'].'"');
            }
            else{
                $this->dbu->query('INSERT INTO `programs_custom_descr` (exercise_id, program_id, description)
                                    VALUES ('.$exercise[$i].', '.$ld['program_id'].', "'.mysql_real_escape_string($ld['description'.$exercise[$i]]).'");');
            }
			
			if(count($secondary_program) && !$is_custom)
			{
				for($j=0; $j<count($secondary_program);$j++)
				{
					$this->dbu->query('INSERT INTO `programs_custom_descr` (exercise_id, program_id, description)
                                    VALUES ('.$exercise[$i].', '.$secondary_program[$j]['program_id'].', "'.mysql_real_escape_string($ld['description'.$exercise[$i]]).'");');
				}
			}
			
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
			
			//update secondary
			if(count($secondary_program) && !$is_custom)
			{
				for($j=0; $j<count($secondary_program);$j++)
				{
					$this->dbu->query("
						 INSERT INTO
							exercise_plan_set 
						 SET
							exercise_plan_id = '".$secondary_program[$j]['program_id']."',
							exercise_program_id = '".$exercise[$i]."',
							plan_description = '".mysql_escape_string($ld['description'.$exercise[$i]])."',
							plan_set_no = '".mysql_escape_string($ld['sets'.$exercise[$i]])."',
							plan_repetitions = '".mysql_escape_string($ld['repetitions'.$exercise[$i]])."',
							plan_time = '".mysql_escape_string($ld['time'.$exercise[$i]])."',
							trainer_id = '".$_SESSION[U_ID]."',
							client_id = '".$secondary_program[$j]['program_id']."',
							is_program_plan = 1
						");
				}
			}
			
			$i++;
		}

		$this->dbu->query("UPDATE exercise_program_plan 
							SET 
								exercise_notes = '".mysql_real_escape_string($ld['exercise_notes'])."',
								date_modified = NOW()
							WHERE
								trainer_id = '".$_SESSION[U_ID]."' AND
								exercise_program_plan_id = '".$ld['program_id']."' 
							");
		
		return true;	
	}
	
	function send_program_email(&$ld)
	{
		$is_appeal_first_name = true;
		
		$this->dbu->query("SELECT title_set, email_set from trainer WHERE trainer_id=".$_SESSION[U_ID]);
        $this->dbu->move_next();
        $appeal_settings = $this->dbu->f('title_set');
        $sendCopy = $this->dbu->f('email_set');
		if($appeal_settings)
		{
			$is_appeal_first_name = false;
		}
		
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
			$ld['error'] = get_template_tag($ld['pag'], $ld['lang'], 'T.PROGRAM_EMPTY');;
			return false;
		}

		if(isset($ld['client_id']) && $ld['client_id'] != '')
		{
			//check exist such program for clienbt
			if(!$this->dbu->field("select count(*) from exercise_plan where exercise_program_id='".$exerciseString."' and client_id=".$ld['client_id'].""))
			{
				$exercise_plan_id=$this->dbu->query_get_id("
							INSERT INTO 
										exercise_plan 
							SET 
										exercise_program_id='".$exerciseString."', 
										date_created=NOW(), 
										date_modified=NOW(), 
										trainer_id='".$_SESSION[U_ID]."', 
										client_id= ".$ld['client_id']." 
							");

				$this->dbu->query("
					SELECT *
					FROM exercise_plan_set
					WHERE exercise_plan_id = ".$ld['program_id']."
				");
				
				while($this->dbu->move_next())
				{
					$this->dbu->query("
						 INSERT INTO
							 exercise_plan_set 
						 SET
							 exercise_plan_id = '".$exercise_plan_id."',
							 exercise_program_id = '".$this->dbu->f('exercise_program_id')."',
							 plan_description = '".mysql_escape_string($this->dbu->f('plan_description'))."',
							 plan_set_no = '".mysql_escape_string($this->dbu->f('plan_set_no'))."',
							 plan_repetitions = '".mysql_escape_string($this->dbu->f('plan_repetitions'))."',
							 plan_time = '".mysql_escape_string($this->dbu->f('plan_time'))."',
							 trainer_id = '".$_SESSION[U_ID]."',
							 client_id = '".$ld['client_id']."',
							 is_program_plan = 0
						");		
				}
			}
		}
		else
		{
			//try check, if such client exists
			$user_exists = false;
			if(trim($ld['email']))
			{
				$this->dbu->query("select * from client where email='".$ld['email']."'");
				while($this->dbu->move_next())
				{
					$user_exists = true;
					$client_id = $this->dbu->f('client_id');
					$trainer_id = $this->dbu->f('trainer_id');
	
					if(!$this->dbu2->field("select count(*) from exercise_plan where exercise_program_id='".$exerciseString."' and client_id=$client_id"))
					{
						$exercise_plan_id=$this->dbu2->query_get_id("
											INSERT INTO 
												exercise_plan 
											SET 
												exercise_program_id='".$exerciseString."', 
												date_created=NOW(), 
												date_modified=NOW(), 
												trainer_id='".$trainer_id."', 
												client_id= ".$client_id." 
											");
		
						$this->dbu2->query("
							SELECT *
							FROM exercise_plan_set
							WHERE exercise_plan_id = ".$ld['program_id']."
						");
						
						while($this->dbu2->move_next())
						{
							$this->dbu3->query("
								 INSERT INTO
									exercise_plan_set 
								 SET
									exercise_plan_id = '".$exercise_plan_id."',
									exercise_program_id = '".$this->dbu2->f('exercise_program_id')."',
									plan_description = '".mysql_escape_string($this->dbu2->f('plan_description'))."',
									plan_set_no = '".mysql_escape_string($this->dbu2->f('plan_set_no'))."',
									plan_repetitions = '".mysql_escape_string($this->dbu2->f('plan_repetitions'))."',
									plan_time = '".mysql_escape_string($this->dbu2->f('plan_time'))."',
									trainer_id = '".$trainer_id."',
									client_id = '".$client_id."',
									is_program_plan = 0
								");		
						}
					}
				}
			}
			
			if(!$user_exists)
			{
				$client_id = $this->dbu->query_get_id("
									INSERT INTO 
										client 
									SET 
										first_name='".mysql_escape_string($ld['first_name'])."', 
										surname='".mysql_escape_string($ld['surname'])."',
										appeal='".mysql_escape_string($ld['appeal'])."',
										email='".mysql_escape_string($ld['email'])."', 
										print_image_type='0', 
										client_note='', 
										create_date=NOW(),
										modify_date=NOW(),
										trainer_id = ".$_SESSION[U_ID]." 
									");
				
				$exercise_plan_id=$this->dbu->query_get_id("
									INSERT INTO 
										exercise_plan 
									SET 
										exercise_program_id='".$exerciseString."', 
										date_created=NOW(), 
										date_modified=NOW(), 
										trainer_id='".$_SESSION[U_ID]."', 
										client_id= ".$client_id." 
									");

				$this->dbu->query("
					SELECT *
					FROM exercise_plan_set
					WHERE exercise_plan_id = ".$ld['program_id']."
				");
				
				while($this->dbu->move_next())
				{
					$this->dbu->query("
						 INSERT INTO
							exercise_plan_set 
						 SET
							exercise_plan_id = '".$exercise_plan_id."',
							exercise_program_id = '".$this->dbu->f('exercise_program_id')."',
							plan_description = '".mysql_escape_string($this->dbu->f('plan_description'))."',
							plan_set_no = '".mysql_escape_string($this->dbu->f('plan_set_no'))."',
							plan_repetitions = '".mysql_escape_string($this->dbu->f('plan_repetitions'))."',
							plan_time = '".mysql_escape_string($this->dbu->f('plan_time'))."',
							trainer_id = '".$_SESSION[U_ID]."',
							client_id = '".$client_id."',
							is_program_plan = 0
						");		
				}
			}
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
            $company_name = $this->dbu->gf('company_name');
	
			$body=$message_data['text'];
			
			if($is_appeal_first_name)
				$body=str_replace('[!APPEAL!]', 'Dear '.$ld['first_name'], $body );
			else
				$body=str_replace('[!APPEAL!]', 'Dear '.$ld['appeal'].' '.$ld['surname'], $body );
			
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
			
			// add pdfs 
			if(!empty($_SESSION['uploaded_pdf_program']))
				foreach($_SESSION['uploaded_pdf_program'] as $att_pdf)
					$mail->AddAttachment("pdf/uploaded_pdf/".$att_pdf, $att_pdf); // attach files/invoice-user-1234.pdf, and rename it to invoice.pdf
			unset($_SESSION['uploaded_pdf_program']);
			
	        $mail->MsgHTML($body);
			$mail->AddAddress($ordermail, $ld['first_name']." ".$ld['surname']);
            if($sendCopy) $mail->AddCC($fromMail, $company_name);
        
	        $mail->Send();
		
			$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.PROG_EMAIL');
			return true;					
		}
		else
		{
			$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.ERROR');
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
			$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_NAME')."<br>";
			$is_ok=false;
		}
		return $is_ok;
	}
	
	function validate_send_program_email(&$ld)
	{
		$is_ok=true;
		if(!$ld['email'])
		{
			$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_EMAIL')."<br>";
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
							SELECT 
								client.client_id
							FROM 
								client 
							WHERE 
								1=1
								AND client_id <> ".$ld['client_id']."
								AND trainer_id = ".$_SESSION[U_ID]." 
								AND first_name = '".mysql_real_escape_string($ld['first_name'])."'
								AND surname = '".mysql_real_escape_string($ld['surname'])."'
								AND email = '".mysql_real_escape_string($ld['email'])."'
								AND print_image_type = '".mysql_real_escape_string($ld['print_image_type'])."'
						");
	    
		if($this->dbu->move_next())
		{
            $ld['error'] = get_template_tag('dashboard', $ld['lang'], 'T.EXIST');
            return false;
		}
		else
		{
			$this->dbu->query("
							UPDATE
										client 
							SET 
										first_name='".mysql_real_escape_string($ld['first_name'])."', 
										surname='".mysql_real_escape_string($ld['surname'])."',
										appeal='".mysql_real_escape_string($ld['appeal'])."',
										email='".mysql_real_escape_string($ld['email'])."', 
										print_image_type='".mysql_real_escape_string($ld['print_image_type'])."', 
										client_note='".mysql_real_escape_string($ld['client_note'])."', 
										modify_date=NOW()
							WHERE 
										client_id = ".mysql_real_escape_string($ld['client_id'])."
								AND
										trainer_id = ".$_SESSION[U_ID]." 
							");
		
			$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.CLIENT_UPDATED');
			return true;
		}
	}
		
	function validate_update_client(&$ld)
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
		if($ld['email'] && !secure_email($ld['email']))
		{
			$ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.PROVIDE_EMAIL')."<br>";
			$is_ok=false;
		}
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
	    
		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.SUCCESS_DELETED');
	
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
		$ld['exercise_id'] = rtrim($ld['exercise_id'],',');
        $ld['exercise_desc'] = mysql_real_escape_string(trim(stripslashes($ld['exercise_desc'])));
        if($ld['exercise_desc'] == 'Notes')
            $ld['exercise_desc'] = '';
		$ld['exercise_plan_id']=$this->dbu->query_get_id("
							INSERT INTO 
								exercise_plan 
							SET 
								exercise_program_id='".$ld['exercise_id']."', 
								date_created=NOW(), 
								date_modified=NOW(), 
								trainer_id='".$_SESSION[U_ID]."', 
								client_id= ".$ld['client_id'].",
								exercise_desc = '".$ld['exercise_desc']."'
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
		
		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.PROG_ADDED');

		return true;
	}

	function update_exercise(&$ld)
	{
		$ld['exercise_id'] = rtrim($ld['exercise_id'],',');
        
		$default_program_desc = get_template_tag('program_update_exercise', $ld['lang'], 'T.PROGRAM_DESC_DEFAULT');

		if($ld['exercise_desc'] == $default_program_desc)
			$ld['exercise_desc'] = '';
		
		$this->dbu->query("
							UPDATE 
								exercise_plan 
							SET 
								exercise_program_id='".$ld['exercise_id']."',
								date_modified=NOW(), 
								trainer_id='".$_SESSION[U_ID]."', 
								client_id= ".$ld['client_id'].",
                                exercise_notes = '".mysql_escape_string($ld['exercise_desc'])."'
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
		
		return true;			
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

		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.EXERCISE_DELETED');
		return true;			
	}

	function generate_pdf(&$ld)
		{
			include_once('php/exercisepdf.php');		
		}
		
	function mail_exercise(&$ld)
	{
		$is_appeal_first_name = true;
        $this->dbu->query("SELECT title_set, email_set from trainer WHERE trainer_id=".$_SESSION[U_ID]);
        $this->dbu->move_next();
        $appeal_settings = $this->dbu->f('title_set');
        $sendCopy = $this->dbu->f('email_set');
		if($appeal_settings)
		{
			$is_appeal_first_name = false;
		}
		
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
			$this->generate_pdf($ld);
			$message_data=get_sys_message('sendpdf');
			$ordermail = $this->dbu->gf('email');
			$fromMail = $this->dbu->gf('trainer_email');
			$replyMail = $message_data['from_email'];
            $company_name = $this->dbu->gf('company_name');
    
			$body=$message_data['text'];

			if($is_appeal_first_name)
			{
				$body=str_replace('[!APPEAL!]', 'Dear '.$this->dbu->f('first_name'), $body );
			}
			else
			{
				$body=str_replace('[!APPEAL!]', 'Dear '.$this->dbu->f('appeal').' '.$this->dbu->f('surname'), $body );
			}
	
			$body=str_replace('[!NAME!]',$this->dbu->f('first_name')." ".$this->dbu->f('last_name'), $body );
			$body=str_replace('[!FIRSTNAME!]',$this->dbu->f('first_name'), $body );
			$body=str_replace('[!SURNAME!]',$this->dbu->f('last_name'), $body );
			$body=str_replace('[!COMPANYNAME!]',$this->dbu->f('company_name'), $body );
			$body=str_replace('[!CLINICNUMBER!]',$this->dbu->f('phone'), $body );
			$body=str_replace('[!CLINICWEBSITE!]',$this->dbu->f('website'), $body );
                
            require_once ('class.phpmailer.php');        
            include_once ("classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
            
            $mail = new PHPMailer();
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
            // 1 = errors and messages
            // 2 = messages only
            $mail->SMTPAuth = true; // enable SMTP authentication
            $mail->Host = SMTP_HOST; // sets the SMTP server
            $mail->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
            $mail->Username = SMTP_USERNAME; // SMTP account username
            $mail->Password = SMTP_PASSWORD; // SMTP account password

            $mail->SetFrom($fromMail, $fromMail);
            //$mail->AddReplyTo($replyMail, $replyMail);
            $mail->Subject = $message_data['subject'];
            
            $mail->AddAttachment("pdf/exercisepdf.pdf", 'exercise_'.$ld['exercise_plan_id'].'.pdf'); // attach files/invoice-user-1234.pdf, and rename it to invoice.pdf
			if(!empty($_SESSION['uploaded_pdf']))
				foreach($_SESSION['uploaded_pdf'] as $att_pdf)
					$mail->AddAttachment("pdf/uploaded_pdf/".$att_pdf, $att_pdf); // attach files/invoice-user-1234.pdf, and rename it to invoice.pdf
			unset($_SESSION['uploaded_pdf']);
			
            $mail->MsgHTML($body);
            
            $mail->AddAddress($ordermail, $this->dbu->gf('first_name')." ".$this->dbu->gf('surname'));
            if($sendCopy) $mail->AddCC($fromMail, $company_name);

            $mail->Send();
                $ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.EXERCISE_SENT');
            return true;					
        }
        else
        {
            $ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_PROFILE');
            return false;								
        }
	}
		
	/* NOT IMPLEMENTED YET */

	function print_exercise(&$ld)
	{
		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.PRINT_EXERCISE');
		return false;
	}
		
	function pdf_exercise(&$ld)
	{
		$ld['error']=get_template_tag($ld['pag'], $ld['lang'], 'T.PDF_EXERCISE');
		return false;
	}
    function modify_program(&$ld){
        $first = isset($ld['first']) ? '&first='.$ld['first'] : '';
        $surname = isset($ld['surname']) ? '&surname='.$ld['surname'] : '';
        $appeal = isset($ld['appeal']) ? '&appeal='.$ld['appeal'] : '';
        $email = isset($ld['email']) ? '&email='.$ld['email'] : '';
        $mode = isset($ld['mode']) ? '&mode='.$ld['mode'] : '';
        //$client_id = isset($ld['client_id']) ? '&client_id='.$ld['client_id'] : '';
        
        $this->dbu->query("SELECT * FROM exercise_program_plan WHERE client_id=".$ld['client_id']." AND parent_plan=".$ld['program_id']);
        if($this->dbu->move_next())
            $plan_copy = $this->dbu->f('exercise_program_plan_id');
        else{
            //copy program plam
            $this->dbu->query("CREATE TEMPORARY TABLE foo AS SELECT * FROM exercise_program_plan WHERE exercise_program_plan_id=".$ld['program_id']);
            $this->dbu->query("UPDATE foo SET exercise_program_plan_id=NULL, client_id=".$ld['client_id'].", parent_plan=".$ld['program_id']);
            $plan_copy = $this->dbu->query_get_id("INSERT INTO exercise_program_plan SELECT * FROM foo;");
            $this->dbu->query("DROP TABLE foo;");
        }
        $_SESSION['modify_program_return_url'] = 'index.php?pag=program_add_patient&client_id='.$ld['client_id'].'&custom_prog_id='.$plan_copy.'&program_id='.$ld['program_id'].$first.$surname.$appeal.$email.$mode;
        
        header("location: /index.php?pag=program_update_exercise&program_id=".$plan_copy);exit;
        exit();
		
    }
	
	function add_to_fav(&$ld)
	{
		if($ld['pid'])
		{
			$cur_date = time();
			if($this->dbu->field("select count(*) from program_fav where program_id=".$ld['pid']." and trainer_id=".$_SESSION[U_ID]))
				$this->dbu->query("delete from program_fav where program_id=".$ld['pid']." and trainer_id=".$_SESSION[U_ID]);
			else
				$this->dbu->query("insert into program_fav set date=$cur_date, program_id=".$ld['pid'].", trainer_id=".$_SESSION[U_ID]);
		}
	}
	
}//end class