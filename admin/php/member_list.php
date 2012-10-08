<?php

/************************************************************************

* @Author: Tinu Coman                                                   *

************************************************************************/

$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "member_list.html"));
$ft->define_dynamic('member_row','main');
$l_r=ROW_PER_PAGE;

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

$where = '';
if($glob['search_key'])
	$where = "and (username like '%".mysql_escape_string($glob['search_key'])."%' or email like '%".$glob['search_key']."%')";

$order_by = ' order by create_date desc, trainer_id asc';

$arguments='';
if($glob['active']==2)
{
	if($glob['trial']==0)
	{
		$dbu->query("select * from trainer where 1 $where and active=2 and is_trial=0 and TIMESTAMPDIFF(MINUTE, '".date("Y-m-d H:i:S")."', expire_date) > 0 $order_by");
		$bannName='Block';
		$pageTitle='Full Paid Members List';
        $ft->assign('SHOW_PASS', '<td height="20" bgcolor="#E8E8E8" width="100" align="center"><strong>Password</strong></td>');
        $show_pass = true;
	}
	else
	{
		$dbu->query("select * from trainer where 1 $where and active=2 and is_trial=1 and TIMESTAMPDIFF(MINUTE, '".date("Y-m-d H:i:S")."', expire_date) > 0 $order_by");	 
		$bannName='Block';
		$pageTitle='Trial Members List';
	}
}
else if($glob['active']==0)
{
	$dbu->query("select * from trainer where 1 $where and (active=0 or TIMESTAMPDIFF(MINUTE, '".date("Y-m-d H:i:S")."', expire_date) < 0) $order_by");	
	$bannName='Trial';
	$pageTitle='Blocked Members List';
}
else if($glob['active']==1)
{
	$dbu->query("select * from trainer where 1  and (active=1 or (active=2 and expire_date='0000-00-00 00:00:00') $where) $order_by");
	$bannName='Block';
	$pageTitle='Members List';
}

$max_rows=$dbu->records_count();

$dbu->move_to($offset);
$i=0;
$t=1;
while($dbu->move_next()&&$i<$l_r)
{
	$trial_name = '';
	$trial_link = '';
	
	$t=$offset+$i+1;

	$expire_date = strtotime($dbu->f('expire_date'));

	if($expire_date>0)
	{
		$expire_time = ($expire_date-time());
		if($expire_time<1)
		{
			$is_trial = "<span style='color:#ff0000; font-weight:bold;'>expired</span>";
			$trial_name = 'Trial';
			$trial_link = "index.php?pag=member_list&act=member-trial&trainer_id=".$dbu->f('trainer_id');
		}
		else if($expire_time>0)
		{
			if($dbu->f('is_trial')==1){
				$is_trial = "trial";
                $extend_3_name = 'Extend to 3 month';
                $extend_3_link = "index.php?pag=member_list&act=member-extend_trial&to=90&active=2&trial=1&trainer_id=".$dbu->f('trainer_id');
                $extend_6_name = 'Extend to 6 month';
                $extend_6_link = "index.php?pag=member_list&act=member-extend_trial&to=180&active=2&trial=1&trainer_id=".$dbu->f('trainer_id');
                $ft->assign('EXTEND_3_NAME',$extend_3_name);
                $ft->assign('EXTEND_3_LINK',$extend_3_link);
                $ft->assign('EXTEND_6_NAME',$extend_6_name);
                $ft->assign('EXTEND_6_LINK',$extend_6_link);
			}
			else if($dbu->f('is_trial')==0){
				$is_trial = "payed";
				$trial_name = 'Trial';
				$trial_link = "index.php?pag=member_list&act=member-trial&trainer_id=".$dbu->f('trainer_id');
			}
		}
	}
	else $is_trial = "<span style='color:#ff0000;'>never used</span>";

	if($dbu->f('is_clinic')==2) $is_clinic = "not set";
	else if($dbu->f('is_clinic')==1) $is_clinic = "clinic";
	else if($dbu->f('is_clinic')==0) $is_clinic = "user";

	$ft->assign('FIRST_NAME',$dbu->f('first_name'));
	$ft->assign('SURNAME',$dbu->f('surname'));
	$ft->assign('USER',$dbu->f('username'));
	$ft->assign('IS_TRIAL', $is_trial );
	$ft->assign('IS_CLINIC', $is_clinic );
	$ft->assign('EMAIL',$dbu->f('email'));
    if(isset( $show_pass) && $show_pass)
        $ft->assign('PASS','<td height="20" align="center" style="color:#777;">'.$dbu->f('password').'</td>');
    

	if($glob['active']==0) $activation="index.php?pag=member_list&active=0&act=member-trial&trainer_id=".$dbu->f('trainer_id');
	else if($glob['active']==1) $activation="index.php?pag=member_list&act=member-deactivate&active=1&trainer_id=".$dbu->f('trainer_id');
	else if($glob['active']==2) $activation="index.php?pag=member_list&act=member-deactivate&active=2&trainer_id=".$dbu->f('trainer_id');

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
	
	
	
    $ft->assign('EDIT_LINK',"index.php?pag=member_add&trainer_id=".$dbu->f('trainer_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=member_list&act=member-delete&trainer_id=".$dbu->f('trainer_id')."&offset=".$b_offset.$arguments);
    $ft->assign('BANN_LINK',$activation);
    $ft->assign('BANN_NAME',$bannName);
	$ft->assign('TRIAL_LINK',$trial_link);
    $ft->assign('TRIAL_NAME',$trial_name);
    $ft->assign('FULLRIGHTS_LINK',"index.php?pag=member_list&act=member-activate_full_rights&active=2&trainer_id=".$dbu->f('trainer_id'));
    $ft->assign('FULLRIGHTS_NAME','No Pay - Full Rights');
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

