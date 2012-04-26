<?php

if(!$is_not_hacked_yet)
{
	exit();
}
$_db=new mysql_db;    
$_db->query("select constant_name, value from settings");
while($_db->move_next())
{
	if($_db->f('constant_name'))
	{
		define($_db->f('constant_name'),$_db->f('value'));
	}
} 

$_db->query("select email from user where user_id='1'");
$_db->move_next();

if(!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL',$_db->f('email'));

?>