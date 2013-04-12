<?php

if(!$is_not_hacked_yet)
{
	exit();
}
$_db=new mysql_db;    
$_db->query("select constant_name, value, long_value, type from settings");
while($_db->move_next())
{
	if($_db->f('constant_name'))
	{
		if($_db->f('type') == 1)
			define($_db->f('constant_name'),$_db->f('value'));
		elseif($_db->f('type') == 2)
			define($_db->f('constant_name'),$_db->f('long_value'));
	}
} 

$_db->query("select email from user where user_id='1'");
$_db->move_next();

if(!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL',$_db->f('email'));

?>