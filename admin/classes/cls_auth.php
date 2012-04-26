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
            $dbu= new mysql_db;
            $dbu->query("select user_id, access_level, password from user where username='".$ld['login']."'");
           if($dbu->move_next())
            { 
        	if($ld['password']==$dbu->f('password'))
                     {
                             session_start();
                             $_SESSION[UID]=1;
                             $_SESSION[U_ID]=$dbu->f('user_id');
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
                $_SESSION[UID]=0;
                $_SESSION[U_ID]=0;
                $_SESSION[ACCESS_LEVEL]=4;
                unset ($ld['act']);
                $ld['pag']='login';
                session_destroy();
          return true;
        }
}//end class
?>
