<?php

/************************************************************************

* @Author: Tinu Coman                                                   *

************************************************************************/

$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "statistic_patient_created.html"));
$ft->define_dynamic('member_row','main');
$l_r=50;//ROW_PER_PAGE;

$dbu=new mysql_db;

if(($glob['ofs']) || (is_numeric($glob['ofs'])))
{
	$glob['offset']=$glob['ofs'];
}

if((!$glob['offset']) || (!is_numeric($glob['offset'])))
{
	$offset=0;
}
else
{
	$offset=$glob['offset'];
	$ft->assign('OFFSET',$glob['offset']);
}

$where = " where 1=1 ";
if(isset($glob['search_key']) && $glob['search_key'])
	$where .= " and username like '%".$glob['search_key']."%' ";

$dbu->query("select count(client.client_id), trainer.* from trainer left join client on client.trainer_id = trainer.trainer_id $where group by trainer.trainer_id order by count(client.client_id) desc, trainer.trainer_id asc");	 
$pageTitle='User Login Statistic';

$max_rows=$dbu->records_count();

$dbu->move_to($offset);
$i=0;
$t=1;
while($dbu->move_next()&&$i<$l_r)
{
	$t=$offset+$i+1;

	
	$ft->assign('FIRST_NAME',$dbu->f('first_name'));
	$ft->assign('SURNAME',$dbu->f('surname'));
	$ft->assign('USER',$dbu->f('username'));
	$ft->assign('PATIENT_COUNT',$dbu->f('count(client.client_id)'));

	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}

	if($offset+1==$max_rows)
	{
		$b_offset=$offset-$l_r;
	}
	else 
	{
		$b_offset=$offset;
	}

    $ft->parse('member_ROW_OUT','.member_row');
  	$i++;
}

if($i==0)
{
    unset($ft);
	return get_error_message("Empty database.");
}

if($offset>=$l_r)
{
	$ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=statistic_patient_created&offset=".($offset-$l_r).$arguments."\">Prev</a>");
}
else
{
	$ft->assign('BACKLINK',''); 
}

if($offset+$l_r<$max_rows)
{
	$ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=statistic_patient_created&offset=".($offset+$l_r).$arguments."\">Next</a>");
}
else
{
	$ft->assign('NEXTLINK','');
}

//*****************JUMP TO FORM***************
$ft->assign('PAG_DD',get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->assign('PAGE',$glob['pag']);
$ft->assign('PAGE_TITLE',$pageTitle);
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('content','main');
$ft->clear_dynamic('content','member_row');

return $ft->fetch('content');

?>

