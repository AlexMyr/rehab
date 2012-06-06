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
							trainer_id,username,password,access_level,active,profile_id,is_clinic,email
						FROM 
							trainer 
						WHERE 
							fb_id = '".$ld['fb_id']."'
						");
		}
		else{
			$query->query("
						SELECT 
							trainer_id,username,password,access_level,active,profile_id,is_clinic,email
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
				$_SESSION[USER_EMAIL] = $query->f('email');
			
				if(isset($ld['store_login']) && $ld['store_login'] == 'on')
				{
					setcookie('UID', 1, 0, '/');
					setcookie('U_ID', $query->f('trainer_id'), 0, '/');
					setcookie('ACCESS_LEVEL', $query->f('access_level'), 0, '/');
					setcookie('USER_EMAIL', $query->f('email'), 0, '/');
				}
				
                
                $this->dbu->query("SELECT * FROM trainer WHERE trainer_id=".$trainer_id);
                $this->dbu->move_next();
                $lang = $this->dbu->f('lang');
                setcookie('language', $lang, 0, '/');
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
				
				if(isset($ld['fb_id']))
				{
					$userEmail = $this->dbu->field("select email from trainer where trainer_id=".$_SESSION[U_ID]);
					if(!$userEmail)
					{
						header("Location: /index.php?pag=profile_edit_email&success=false&error=".urlencode("You have not email address, please fill this field."));
						exit;
					}
				}
				
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
		
		setcookie('UID', "", time() - 3600, '/');
		setcookie('U_ID', "", time() - 3600, '/');
		setcookie('ACCESS_LEVEL', "", time() - 3600, '/');
		setcookie('USER_EMAIL', "", time() - 3600, '/');
		
		if(isset($_SESSION['fb_login_rmp']))
			$_SESSION['fb_login_rmp'] = 0;
		
		$ld['pag'] = 'cms';
		
		return true;
	}
	
	function login_fb()
	{
		require 'fb/facebook.php';

		$facebook = new Facebook(array(
		  'appId'  => '140136112789193',
		  'secret' => 'dfa5828ecca71050bfd974f659003a66',
		));
		
		// See if there is a user from a cookie
		$user = $facebook->getUser();

		if ($user) {
		  try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');
		  } catch (FacebookApiException $e) {
			$user = null;
		  }
		}
		
		if($user)
		{
			$_SESSION['fb_login_rmp'] = 1;
			header("Location: /index.php?act=auth-login&pag=login&fb_id=".$user);
		}
		else
		{
			header("Location: ".$facebook->getLoginUrl());
		}

	}
	
}//end class