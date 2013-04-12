<?php
/*                                                                                                                                               *
*************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
class auth
{
	/****************************************************************
	* function login(&$ld)                                          *
	****************************************************************/

	function login(&$ld)
	{
		session_start();

		$dbu= new mysql_db;
		$dbu->query("select user_id, access_level, password from user where username='".$ld['login']."'");
		if($dbu->move_next())
		{ 
			if($ld['password']==$dbu->f('password'))
			{
				$_SESSION[UID]=1;
				$_SESSION[U_ID]=$dbu->f('user_id');
				$_SESSION['admin_access']=$dbu->f('access_level');
				$_SESSION[ACCESS_LEVEL]=$dbu->f('access_level');
				$ld['pag']='welcome';
				return true;
			}
		}
		$ld['error'].="Wrong Username or Password<br>";
		return false;
	}
	
	/****************************************************************
	* function logout(&$ld)                                         *
	****************************************************************/
	
	function logout(&$ld)
	{
		session_start();
		$_SESSION[UID]=0;
		$_SESSION[U_ID]=0;
		$_SESSION['admin_access']=4;
		//$_SESSION[ACCESS_LEVEL]=4;
		unset ($ld['act']);
		$ld['pag']='login';
		//session_destroy();
		return true;
	}
}//end class
?>
