<?php

define("ROOT_PATH", dirname(dirname(dirname(__FILE__))));
require_once(ROOT_PATH.'/phpexcel/PHPExcel.php');
require_once(ROOT_PATH.'/phpexcel/PHPExcel/Writer/Excel2007.php');
//require_once(ROOT_PATH.'/phpexcel/PHPExcel/IOFactory.php');
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("RMP");
$objPHPExcel->getProperties()->setLastModifiedBy("RMP");
$objPHPExcel->getProperties()->setTitle("RMP USERS");
$objPHPExcel->getProperties()->setSubject("RMP USERS");
$objPHPExcel->getProperties()->setDescription("RMP USERS");
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Email');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'First Name');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Surname');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'Company Name');


$dbu=new mysql_db;

$i=2;
$dbu->query("select t.email as lemail, thp.* from trainer t left join trainer_header_paper thp on t.trainer_id=thp.trainer_id order by t.trainer_id asc");
while($dbu->move_next())
{
	if(!$dbu->f('email')) continue;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $dbu->f('lemail'));
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $dbu->f('first_name'));
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $dbu->f('surname'));
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $dbu->f('company_name'));
	$i++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="rmp_users.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output'); 
exit;

//$file = fopen('user_info.txt', 'w+');
//if($file)
//{
//	while($dbu->move_next())
//	{
//		fwrite($file, $dbu->f('email')."\n");	
//	}
//	fclose($file);
//}
//
//$path = $_SERVER['DOCUMENT_ROOT'].'/admin/user_info.txt';
//$path = pathinfo($path);
//header("Content-type: application/octet-stream");
//header("Content-Disposition: filename=\"".$path["basename"]."\"");
//// Send the file contents.
//set_time_limit(0);
//readfile($_SERVER['DOCUMENT_ROOT'].'/admin/user_info.txt');
//exit;
?>