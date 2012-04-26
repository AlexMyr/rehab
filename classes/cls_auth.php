<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
class auth
{
	var $dbu;
	
	function auth()
	{
		$this->dbu = new mysql_db();
	}
	/****************************************************************
	* function login(&$ld)                                          *
	****************************************************************/
	function login(&$ld)
	{
		global $user_level;

		$query = $this->dbu;
		
		if(isset($ld['fb_id']))
		{
			$query->query("
						SELECT 
							trainer_id,username,password,access_level,active,profile_id,is_clinic
						FROM 
							trainer 
						WHERE 
							fb_id = '".$ld['fb_id']."'
						");
		}
		else{
			$query->query("
						SELECT 
							trainer_id,username,password,access_level,active,profile_id,is_clinic
						FROM 
							trainer 
						WHERE 
							username = '".$ld['username']."' AND password = '".$ld['password']."'
						");
		}
		
		if($query->move_next())
		{
			$trainer_id = $query->f('trainer_id');
			
			if($query->f('active')==0)
			{
				$ld['error'] = 'Username was banned for a reason. Please contact support for more details!';
				return false;							
			}
			else if(($ld['password'] == $query->f('password') || $ld['fb_id'] == $query->f('fb_id')) && $query->f('is_login')==0 && $query->f('active')!=0)
			{
				//session_cache_limiter('private');
				session_start();
				$_SESSION[UID]=1;
				$_SESSION[U_ID] = $query->f('trainer_id');
				$_SESSION[ACCESS_LEVEL] = $query->f('access_level');
				//$_SESSION[ACCESS_LEVEL] = '3';							
				global $user_level;

				$user_level = $_SESSION[ACCESS_LEVEL];
				if($query->f('active')==1)
				{
					$set_trial_time = date('Y-m-d H:i:s',strtotime('+30days'));
					$this->dbu->query("
										UPDATE 
											trainer 
										SET 
											active=2, 
											expire_date='".$set_trial_time."',
											ip='".$_SERVER['REMOTE_ADDR']."'  
										WHERE 
											trainer_id = ".$query->f('trainer_id')." 
									");
					$ld['pag'] = 'profile_choose_clinic';
				}
				else if($query->f('active')==2 && $query->f('profile_id')==0)
				{
					$this->dbu->query("
										UPDATE 
											trainer 
										SET  
											ip='".$_SERVER['REMOTE_ADDR']."' 
										WHERE 
											trainer_id = ".$query->f('trainer_id')." 
									");
					/*
					check if is single user licence or clinic
					not set = 2 [default]
					single = 0
					clinic = 1
					*/
					if($query->f('is_clinic')==2) $ld['pag'] = 'profile_choose_clinic';
					else if($query->f('is_clinic')!=2) $ld['pag'] = 'profile';
				}
				else if($query->f('active')!=0)
				{
					
					$this->dbu->query("
										UPDATE 
											trainer 
										SET  
											ip='".$_SERVER['REMOTE_ADDR']."' 
										WHERE 
											trainer_id = ".$query->f('trainer_id')." 
									");
					if($query->f('is_clinic')==2) $ld['pag'] = 'profile_choose_clinic';
					else if($query->f('is_clinic')!=2) $ld['pag'] = 'profile';
				}
				
				$this->dbu->query("
										UPDATE 
											trainer 
										SET  
											last_login_date='".time()."' 
										WHERE 
											trainer_id = ".$trainer_id." 
									");
				
				return true;
			}
			/*else if($query->f('is_login')==1)
				{
					$ld['error'] = 'User already logged in';
					return false;
				}*/
		}
		$ld['error'] = 'Username or password invalid';
		return false;
	}
	 
	/****************************************************************
	* function logout(&$ld)                                         *
	****************************************************************/
	function logout(&$ld)
	{
		$this->dbu->query("
							UPDATE 
								trainer 
							SET 
								ip=' ' 
							WHERE 
								trainer_id = '".$_SESSION[U_ID]."' 
						");
		session_register(UID);
		$_SESSION[UID]=0;
		$_SESSION[U_ID]=0;
		//$_SESSION[ACCESS_LEVEL]=4;
		$_SESSION[ACCESS_LEVEL]=4;
		if (isset($_COOKIE[session_name()])){
			$params = session_get_cookie_params();
			if(!setcookie(session_name(), 'trt',time()-1,$params["path"], $params["domain"], $params["secure"], $params["httponly"])){
				$ld['error'] = 'not deleted';
				return false;
			}
		}
		session_destroy();
		$ld['pag'] = 'cms';
		
		return true;
	}
	
}//end class