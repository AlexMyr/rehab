<?php
$dbu=new mysql_db;

$dbu->query("select email from trainer where expire_date<NOW() order by trainer_id asc");	 
$file = fopen('expired_user_info.txt', 'w+');
if($file)
{
	while($dbu->move_next())
	{
		if($dbu->f('email'))
			fwrite($file, $dbu->f('email')."\n");	
	}
	fclose($file);
}

$path = $_SERVER['DOCUMENT_ROOT'].'/admin/expired_user_info.txt';
$path = pathinfo($path);
header("Content-type: application/octet-stream");
header("Content-Disposition: filename=\"".$path["basename"]."\"");
// Send the file contents.
set_time_limit(0);
readfile($_SERVER['DOCUMENT_ROOT'].'/admin/expired_user_info.txt');
exit;
?>