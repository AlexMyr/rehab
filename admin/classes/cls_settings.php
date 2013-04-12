<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/
class settings
{
  var $dbu;

function settings()
        {
                $this->dbu=new mysql_db;
        }

/**********************************************************************
* function admin_update(&$ld)                                         *
**********************************************************************/
function admin_update(&$ld)
	{

	   if(!$this->admin_update_validate($ld))
	   	{
	   		return false;
	   	}
	   	
		$this->dbu->query("update settings set 
						   value='".$ld['row_per_page']."'
						   where
	   					   constant_name='ROW_PER_PAGE'"
	   					);	
	   	$this->dbu->query("update settings set 
						   value='".$ld['email']."'
						   where
	   					   constant_name='ADMIN_EMAIL'"
	   					);	
	   								
	    $this->dbu->query("update user set 
	   					   username='".$ld['username']."',
	   					   email='".$ld['email']."',
	   					   password='".$ld['password']."'
	   					   where
	   					   user_id='".$_SESSION[U_ID]."'	   						
	   					");	
		$this->dbu->query("update settings set 
						   value='".$ld['smtp_host']."'
						   where
	   					   constant_name='SMTP_HOST'"
	   					);	
		$this->dbu->query("update settings set 
						   value='".$ld['smtp_port']."'
						   where
	   					   constant_name='SMTP_PORT'"
	   					);	
		$this->dbu->query("update settings set 
						   value='".$ld['smtp_username']."'
						   where
	   					   constant_name='SMTP_USERNAME'"
	   					);	
		$this->dbu->query("update settings set 
						   value='".$ld['smtp_password']."'
						   where
	   					   constant_name='SMTP_PASSWORD'"
	   					);
		
		$this->dbu->query("update settings set 
						   value='".$ld['paypal_username']."'
						   where
	   					   constant_name='PAYPAL_USERNAME'"
	   					);
		$this->dbu->query("update settings set 
						   value='".$ld['paypal_password']."'
						   where
	   					   constant_name='PAYPAL_PASSWORD'"
	   					);
		$this->dbu->query("update settings set 
						   value='".$ld['paypal_sign']."'
						   where
	   					   constant_name='PAYPAL_SIGN'"
	   					);	
	   				
		$ld['error'].="Settings successfully updated.";
	   	return true;
	}

	
	
/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/

function admin_update_validate(&$ld)
{
        $is_ok=true;
        if(!$ld['username'])
        {
                $ld['error'].="Please fill in the 'Admin User' field."."<br>";
                $is_ok=false;
        }
        else 
        {
        	$this->dbu->query("select user_id from user where username='".$ld['username']."' and user_id!='".$_SESSION[U_ID]."'");
        	if($this->dbu->move_next())
        	{
                $ld['error'].="There is another Administrative Account with this username. Please change it."."<br>";
                $is_ok=false;
	       	}
        }
        if(!$ld['email'])
        {
                $ld['error'].="Please fill in the 'Email' Field."."<br>";
                $is_ok=false;
        }
        if($ld['email'] && !secure_email($ld['email']))
        {
                $ld['error'].="Please fill in the 'Email' Field with a valid email address."."<br>";
                $is_ok=false;
        }
        
		if(!$ld['row_per_page'])
		{
                $ld['error'].="Please fill in the 'Rows Per Page' field.<br>";
                $is_ok=false;
		}
		elseif(!is_numeric($ld['row_per_page']))
		{
                $ld['error'].="Please fill in the 'Rows Per Page' field with a numeric value.<br>";
                $is_ok=false;
		}
        if ($ld['password']!=$ld['password1'])
        {
                $ld['error'].="Password Doesn't match."."<br>";
                $is_ok=false;
        }
        
        if($is_ok && !$ld['password'])
        {
        		$this->dbu->query("select password from user where user_id='".$_SESSION[U_ID]."'");
        		$this->dbu->move_next();
        		$ld['password']=$this->dbu->f('password');
        }
        if (!$ld['smtp_host'])
        {
                $ld['error'].="Please fill in the SMTP HOST field."."<br>";
                $is_ok=false;
        }
        if (!$ld['smtp_port'])
        {
                $ld['error'].="Please fill in the SMTP PORT field."."<br>";
                $is_ok=false;
        }
        
        
	return $is_ok;	
}

  function update_banner_settings(&$ld)
  {

	$show_banner = 0;
	if(isset($ld['show_banner']))
	  $show_banner = 1;
	  
	$this->dbu->query("update settings set 
						   value='".$show_banner."'
						   where
	   					   constant_name='SHOW_BANNER'"
	   					);
	
	$this->dbu->query("update settings set 
						   long_value='".mysql_real_escape_string($ld['banner_content'])."'
						   where
	   					   constant_name='BANNER_CONTENT'"
	   					);	
	   				
	$ld['error'].="Banner settings successfully updated.";
	return true;
  }

}//end class
?>