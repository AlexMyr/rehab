<?php

/************************************************************************

* @Author: Tinu Coman                                                   *

************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "member.html"));
$ft->define_dynamic('member_row','main');
$l_r=10;
$ft->assign('PAG', 'member');

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

if(isset($glob['search_key']))
{
  $search_key = mysql_escape_string(trim($glob['search_key']));
  
  $dbu=new mysql_db;
  if($glob['search_key'])
  {
    $dbu->query("select * from trainer where username like '%".$search_key."%' or email like '%".$search_key."%' order by create_date desc, trainer_id asc");
    
    $max_rows=$dbu->records_count();

    $dbu->move_to($offset);
    $i=0;
    $t=1;
    
    while($dbu->move_next()&&$i<$l_r)
    {
      if($dbu->f('active')==2)
      {
        if($dbu->f('trial')==0)
            $bannName='Block';
        else
          $bannName='Block';
      }
      else if($dbu->f('active')==0)
          $bannName='Activate';
      else if($dbu->f('active')==1)
          $bannName='Block';
    
      $trial_name = '';
      $trial_link = '';
	
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
          //if($expire_time<1) $is_trial = "<span style='color:#ff0000; font-weight:bold;'>expired</span>";
          else if($expire_time>0)
          {
              if($dbu->f('is_trial')==1)
                $is_trial = "trial";
              else if($dbu->f('is_trial')==0)
              {
                $is_trial = "payed";
                $trial_name = 'Trial';
				$trial_link = "index.php?pag=member_list&act=member-trial&trainer_id=".$dbu->f('trainer_id');
              }
              
          }
      }
      else $is_trial = "<span style='color:#ff0000;'>never used</span>";
		
		$extend_3_name = 'Extend to 3 month';
		$extend_3_link = "index.php?pag=member_list&act=member-extend_trial&to=90&active=2&trial=1&trainer_id=".$dbu->f('trainer_id');
		$extend_6_name = 'Extend to 6 month';
		$extend_6_link = "index.php?pag=member_list&act=member-extend_trial&to=180&active=2&trial=1&trainer_id=".$dbu->f('trainer_id');
		$extend_12_name = 'Extend to 1 year';
        $extend_12_link = "index.php?pag=member_list&act=member-extend_trial&to=365&active=2&trial=1&trainer_id=".$dbu->f('trainer_id');
		$ft->assign('EXTEND_3_NAME',$extend_3_name);
		$ft->assign('EXTEND_3_LINK',$extend_3_link);
		$ft->assign('EXTEND_6_NAME',$extend_6_name);
		$ft->assign('EXTEND_6_LINK',$extend_6_link);
		$ft->assign('EXTEND_12_NAME',$extend_12_name);
        $ft->assign('EXTEND_12_LINK',$extend_12_link);
	  
      if($dbu->f('is_clinic')==2) $is_clinic = "not set";
      else if($dbu->f('is_clinic')==1) $is_clinic = "clinic";
      else if($dbu->f('is_clinic')==0) $is_clinic = "user";
      
      if($dbu->f('active')==0) $activation="index.php?pag=member_list&active=0&act=member-activate&trainer_id=".$dbu->f('trainer_id');
      else if($dbu->f('active')==1) $activation="index.php?pag=member_list&act=member-deactivate&active=1&trainer_id=".$dbu->f('trainer_id');
      else if($dbu->f('active')==2) $activation="index.php?pag=member_list&act=member-deactivate&active=2&trainer_id=".$dbu->f('trainer_id');
      
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
      
      $ft->assign('FIRST_NAME',$dbu->f('first_name'));
      $ft->assign('SURNAME',$dbu->f('surname'));
      $ft->assign('USER',$dbu->f('username'));
      $ft->assign('IS_TRIAL', $is_trial );
      $ft->assign('IS_CLINIC', $is_clinic );
      $ft->assign('EMAIL',$dbu->f('email'));
      $ft->assign('PASS',$dbu->f('password'));
      $ft->assign('BG_COLOR',"#F8F9FA");

      $ft->assign('EDIT_LINK',"index.php?pag=member_add&trainer_id=".$dbu->f('trainer_id'));
      $ft->assign('DELETE_LINK',"index.php?pag=member_list&act=member-delete&trainer_id=".$dbu->f('trainer_id'));
      $ft->assign('BANN_LINK',$activation);
      $ft->assign('BANN_NAME',$bannName);
      $ft->assign('TRIAL_LINK',$trial_link);
      $ft->assign('TRIAL_NAME',$trial_name);
      $ft->assign('FULLRIGHTS_LINK',"index.php?pag=member_list&act=member-activate_full_rights&active=2&trainer_id=".$dbu->f('trainer_id'));
      $ft->assign('FULLRIGHTS_NAME','No Pay - Full Rights');
      $ft->parse('member_ROW_OUT','.member_row');
      $i++;
    }
  }
}

if($offset>=$l_r)
{
	$ft->assign('BACKLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=member&search_key=".$glob['search_key']."&offset=".($offset-$l_r)."\">Prev</a>");
}
else
{
	$ft->assign('BACKLINK',''); 
}

if($offset+$l_r<$max_rows)
{
	$ft->assign('NEXTLINK',"<a class=\"RedBoldLink\" href=\"index.php?pag=member&search_key=".$glob['search_key']."&offset=".($offset+$l_r)."\">Next</a>");
}
else
{
	$ft->assign('NEXTLINK','');
}

$ft->assign('PAGE_TITLE',"Search Members");
//*****************JUMP TO FORM***************
$ft->assign('PAG_DD',get_pagination_dd($offset, $max_rows, $l_r, $glob));
//*****************JUMP TO FORM***************

$ft->parse('CONTENT','main');
$ft->clear_dynamic('content','member_row');
return $ft->fetch('CONTENT');

?>