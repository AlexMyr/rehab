<?php
$dbu=new mysql_db;

$dbu->query("select email from trainer order by trainer_id asc");	 
$file = fopen('user_info.txt', 'w+');
if($file)
{
	while($dbu->move_next())
	{
		if(trim($dbu->f('email')))
			fwrite($file, $dbu->f('email')."\r\n");	
	}
	fclose($file);
}

$path = $_SERVER['DOCUMENT_ROOT'].'/admin/user_info.txt';
$path = pathinfo($path);
header("Content-type: application/octet-stream");
header("Content-Disposition: filename=\"".$path["basename"]."\"");
// Send the file contents.
set_time_limit(0);
readfile($_SERVER['DOCUMENT_ROOT'].'/admin/user_info.txt');
exit;
?>