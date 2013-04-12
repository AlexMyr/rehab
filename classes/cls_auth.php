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
		session_start();
	}
	
	function login_session($trainer_id, $access_level, $email)
	{
		$_SESSION[UID]=1;
		$_SESSION[U_ID] = $trainer_id;
		$_SESSION[ACCESS_LEVEL] = $access_level;
		$_SESSION[USER_EMAIL] = $email;
	}
	
	function login_cookie($trainer_id, $access_level, $email)
	{
		setcookie('UID', 1, 0, '/');
		setcookie('U_ID', $trainer_id, 0, '/');
		setcookie('ACCESS_LEVEL', $access_level, 0, '/');
		setcookie('USER_EMAIL', $email, 0, '/');
	}
	
	function update_login_date($trainer_id)
	{
		$this->dbu->query("
										UPDATE 
											trainer 
										SET  
											last_login_date='".time()."' 
										WHERE 
											trainer_id = ".$trainer_id." 
									");
	}
	
	/****************************************************************
	* function login(&$ld)                                          *
	****************************************************************/
	function login(&$ld)
	{
		global $user_level;
		$ld['username'] = trim($ld['username']);
		$ld['password'] = trim($ld['password']);
		
		if((!$ld['username'] || !$ld['password']) && !isset($ld['fb_id']))
		{
			$ld['error'] = 'Username or password invalid';
			$ld['pag'] = 'signup';
			return false;
		}
		
		$query = $this->dbu;
		
		if(isset($ld['fb_id']))
		{
			$query->query("
						SELECT 
							trainer_id,username,password,access_level,active,profile_id,is_clinic,email,fb_id,is_login,expire_date,lang
						FROM 
							trainer 
						WHERE 
							fb_id = '".$ld['fb_id']."'
						");
		}
		else{
			$query->query("
						SELECT 
							trainer_id,username,password,access_level,active,profile_id,is_clinic,email,fb_id,is_login,expire_date,lang
						FROM 
							trainer 
						WHERE 
							username = '".mysql_real_escape_string($ld['username'])."' AND password = '".mysql_real_escape_string($ld['password'])."'
						");
		}
	
		if($query->move_next())
		{
			$trainer_id = $query->f('trainer_id');
			$lang = $query->f('lang');
            setcookie('language', $lang, 0, '/');
			
			$this->update_login_date($trainer_id);
			
			if($query->f('active')==0)
			{
				if(strtotime($query->f('expire_date'))-time()<=0)
				{
					//if account expired
					$this->login_session($query->f('trainer_id'), $query->f('access_level'), $query->f('email'));

					global $user_level;
					$user_level = $_SESSION[ACCESS_LEVEL];
					
					$ld['error'] = 'Your account has been expired!';
					$ld['pag'] = 'profile_payment';
					return false;
				}
				else
				{
					//if account banned
					$ld['error'] = 'Username was banned for a reason. Please contact support for more details!';
					$ld['pag'] = 'login';
					return false;
				}
			}
			elseif($query->f('active')==1)
			{
				$set_trial_time = date('Y-m-d H:i:s',strtotime('+14days'));
				$this->dbu->query("
									UPDATE 
										trainer 
									SET 
										active=2, 
										expire_date='".mysql_real_escape_string($set_trial_time)."',
										ip='".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."'  
									WHERE 
										trainer_id = ".mysql_real_escape_string($trainer_id)." 
								");
				
				//if account just registered
				$this->login_session($trainer_id, $query->f('access_level'), $query->f('email'));
				if(isset($ld['store_login']) && $ld['store_login'] == 'on')
					$this->login_cookie($trainer_id, $query->f('access_level'), $query->f('email'));
				
				global $user_level;
				$user_level = $_SESSION[ACCESS_LEVEL];
				
				if($query->f('is_clinic') == 2)
				{
					$ld['pag'] = 'profile_choose_clinic';
					$ld['error'] = 'Please fill this field.';
				}
				else
				{
					$ld['pag'] = 'dashboard';
					$ld['error'] = 'You logged in succesfully!';
				}
				return true;
			}
			elseif($query->f('active')==2)
			{
				//if account trial or payed
				$this->login_session($trainer_id, $query->f('access_level'), $query->f('email'));
				if(isset($ld['store_login']) && $ld['store_login'] == 'on')
					$this->login_cookie($trainer_id, $query->f('access_level'), $query->f('email'));

				global $user_level;
				$user_level = $_SESSION[ACCESS_LEVEL];
				
				if($query->f('is_clinic') == 2)
				{
					//if account choose neither clinic nor single user
					$ld['pag'] = 'profile_choose_clinic';
					$ld['error'] = 'Please fill this field.';
					return false;
				}
				
				if(!$query->f('email'))
				{
					//if account has no email redirect to edit email page
					$ld['pag'] = 'profile_edit_email';
					$ld['error'] = 'You have not email address, please fill this field.';
					return false;
				}
					
				$ld['pag'] = 'dashboard';
				$ld['error'] = 'You logged in succesfully!';
				return true;
			}
			else
			{
				$ld['pag'] = 'login';
				$ld['error'] = 'Username or password invalid.';
				return false;
			}
		}
		$ld['error'] = 'Username or password invalid';
		return false;
	}
	
	function login_ajax(&$ld)
	{
		global $user_level;
		$ld['username'] = trim($ld['username']);
		$ld['password'] = trim($ld['password']);
		
		if(!$ld['username'] || !$ld['password'])
		{
			$ld['error'] = 'Username or password invalid';
			return false;
		}
		
		$query = $this->dbu;
		
		$query->query("
					SELECT 
						trainer_id,username,password,access_level,active,profile_id,is_clinic,email,fb_id,is_login,expire_date,lang
					FROM 
						trainer 
					WHERE 
						username = '".mysql_real_escape_string($ld['username'])."' AND password = '".mysql_real_escape_string($ld['password'])."'
					");
		
		if($query->move_next())
		{
			$trainer_id = $query->f('trainer_id');
			$lang = $query->f('lang');
            setcookie('language', $lang, 0, '/');
			
			$this->update_login_date($trainer_id);
			
			if($query->f('active')==0)
			{
				if(strtotime($query->f('expire_date'))-time()<=0)
				{
					//if account expired
					$this->login_session($trainer_id, $query->f('access_level'), $query->f('email'));

					global $user_level;
					$user_level = $_SESSION[ACCESS_LEVEL];
					
					$ld['error'] = 'Your account has been expired!';
					$ld['pag_redir'] = 'profile_payment';
					return false;
				}
				else
				{
					//if account banned
					$ld['error'] = 'Username was banned for a reason. Please contact support for more details!';
					$ld['pag_redir'] = 'login';
					return false;
				}
			}
			elseif($query->f('active')==1)
			{
				$set_trial_time = date('Y-m-d H:i:s',strtotime('+14days'));
				
				$this->dbu->query("
									UPDATE 
										trainer 
									SET 
										active=2, 
										expire_date='".mysql_real_escape_string($set_trial_time)."',
										ip='".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."'  
									WHERE 
										trainer_id = ".mysql_real_escape_string($trainer_id)." 
								");
				
				//if account just registered
				$this->login_session($trainer_id, $query->f('access_level'), $query->f('email'));
				if(isset($ld['store_login']) && $ld['store_login'] == 'on')
					$this->login_cookie($trainer_id, $query->f('access_level'), $query->f('email'));
				
				global $user_level;
				$user_level = $_SESSION[ACCESS_LEVEL];
				
				if($query->f('is_clinic') == 2)
				{
					$ld['pag_redir'] = 'profile_choose_clinic';
					$ld['error'] = 'Please fill this field.';
				}
				else
				{
					$ld['pag_redir'] = 'dashboard';
					$ld['error'] = 'You logged in succesfully!';
				}
				return true;
			}
			elseif($query->f('active')==2)
			{
				//if account trial or payed
				$this->login_session($trainer_id, $query->f('access_level'), $query->f('email'));
				if(isset($ld['store_login']) && $ld['store_login'] == 'on')
					$this->login_cookie($trainer_id, $query->f('access_level'), $query->f('email'));
				
				global $user_level;
				$user_level = $_SESSION[ACCESS_LEVEL];
				
				if($query->f('is_clinic') == 2)
				{
					//if account choose neither clinic nor single user
					$ld['pag_redir'] = 'profile_choose_clinic';
					$ld['error'] = 'Please fill this field.';
					return false;
				}
				
				if(!$query->f('email'))
				{
					//if account has no email redirect to edit email page
					$ld['pag_redir'] = 'profile_edit_email';
					$ld['error'] = 'You have not email address, please fill this field.';
					return false;
				}
					
				$ld['pag_redir'] = 'dashboard';
				$ld['error'] = 'You logged in succesfully!';
				return true;
			}
			else
			{
				$ld['pag_redir'] = 'login';
				$ld['error'] = 'Username or password invalid.';
				return false;
			}
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
		//session_register(UID);
		$_SESSION[UID]=0;
		$_SESSION[U_ID]=0;
		$_SESSION[ACCESS_LEVEL]=4;
		
		//if (isset($_COOKIE[session_name()])){
		//	$params = session_get_cookie_params();
		//	if(!setcookie(session_name(), 'trt',time()-1,$params["path"], $params["domain"], $params["secure"], $params["httponly"])){
		//		$ld['error'] = 'not deleted';
		//		return false;
		//	}
		//}
		//session_destroy();
		
		setcookie('UID', "", time() - 3600, '/');
		setcookie('U_ID', "", time() - 3600, '/');
		setcookie('ACCESS_LEVEL', "", time() - 3600, '/');
		setcookie('USER_EMAIL', "", time() - 3600, '/');
		
		if(isset($_SESSION['fb_login_rmp']))
			$_SESSION['fb_login_rmp'] = 0;
		
		$ld['pag'] = 'cms';
		$_SESSION['set_fb_login'] = 0;
		return true;
	}
	
	function login_fb()
	{

		require 'fb/facebook.php';

		$facebook = new Facebook(array(
		  'appId'  => '264894086933472',
		  'secret' => '9e61aeb9b7632060cc834d6ad4b106a3',
		  'cookie' => true,
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
			if(!$_SESSION['set_fb_login'])
			{
				$_SESSION['set_fb_login'] = 1;
				header("Location: ".$facebook->getLoginUrl(array("scope" => "email")));
			}
			else
			{
				$_SESSION['fb_login_rmp'] = 1;
				$user = $facebook->getUser();
				header("Location: /index.php?act=auth-login&pag=login&fb_id=".$user);
			}
		}

	}
	
}//end class