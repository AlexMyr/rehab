<?php

/************************************************************************

* @Author: Tinu Coman                                                   *

************************************************************************/
$pageTitle='Members Logs';
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "member_logs.html"));
$ft->define_dynamic('member_row','main');

$l_r = ROW_PER_PAGE;

$dbu = new mysql_db;
$dbu1 = new mysql_db;

if(($glob['ofs']) || (is_numeric($glob['ofs'])))
	$glob['offset']=$glob['ofs'];

if((!$glob['offset']) || (!is_numeric($glob['offset'])))
{
	$offset=0;
}
else
{
	$offset=$glob['offset'];
	$ft->assign('OFFSET',$glob['offset']);
}

$order_by = ' order by trainer_prolong_log.date desc, trainer_prolong_log.trainer_id asc';
$dbu->query("select * from trainer_prolong_log left join trainer on trainer_prolong_log.trainer_id=trainer.trainer_id where 1 $order_by");

$max_rows=$dbu->records_count();
$dbu->move_to($offset);
$i=0;

while($dbu->move_next() && $i<$l_r)
{

	if($offset+1==$max_rows)
		$b_offset=$offset-$l_r;
	else 
		$b_offset=$offset;
	
	if($i%2==1)
		$ft->assign('BG_COLOR',"#F8F9FA");
	else
		$ft->assign('BG_COLOR',"#FFFFFF");
	
	$ft->assign('USERNAME', $dbu->f('username'));
	$ft->assign('TYPE', $dbu->f('type'));
	$ft->assign('PERIOD', $dbu->f('time_prolonged'));
	$ft->assign('DATE', date('m/d/Y', $dbu->f('date')));
	$ft->parse('member_ROW_OUT','.member_row');
  	$i++;
}

if($i==0)
{
    unset($ft);
	return get_error_message("Search list empty.");
}

if($offset>=$l_r)
{
	$ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=member_list&offset=".($offset-$l_r).$arguments."\">Prev</a>");
}
else
{
	$ft->assign('BACKLINK',''); 
}

if($offset+$l_r<$max_rows)
{
	$ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=member_list&offset=".($offset+$l_r).$arguments."\">Next</a>");
}
else
{
	$ft->assign('NEXTLINK','');
}

//*****************JUMP TO FORM***************
$ft->assign('PAG_DD', get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->assign('PAGE',$glob['pag']);
$ft->assign('PAG',$glob['pag']);
$ft->assign('ACTIVE',$glob['active']);
$ft->assign('TRIAL',$glob['trial']);
$ft->assign('PAGE_TITLE',$pageTitle);
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('content','main');
$ft->clear_dynamic('content','member_row');

return $ft->fetch('content');

?>

