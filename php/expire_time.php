<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$ft->define(array('main' => "expire_time.html"));

if($_SESSION[U_ID])
{
    $dbu = new mysql_db();
    
    $get_time = $dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");
    
    $get_time->next();
    if($get_time->f('is_trial')==1)
    {
        $tags = get_template_tag('expire_time', $glob['lang']);

        $expire_time = (strtotime($get_time->f('expire_date'))-time());
        
        $expire_days = intval(intval($expire_time) / (3600 * 24));
        $expire_hours = intval(intval($expire_time) / 3600);
        $expire_minutes = (intval(intval($expire_time) / 60) % 60);
        
        if($expire_days>0 && $expire_days>1) $time_remained = $tags['T.IN']." <strong>".$expire_days." ".$tags['T.DAYS']."</strong>"; 
        else if($expire_days>0 && $expire_days==1) $time_remained = $tags['T.IN']." <strong>".$expire_days." ".$tags['T.DAY']."</strong>"; 
        
        else if($expire_days<1 && $expire_minutes>0) $time_remained = "<strong>".$tags['T.TODAY']."</strong>"; 
        
        if(!SHOW_BANNER)
            $display_expire_time = '';
        else
            $display_expire_time = 'display:none;';
        
        if($expire_days<1 && $expire_minutes<1)
        {
            $dbu->query("UPDATE trainer SET active=0 WHERE trainer_id=".$_SESSION[U_ID]." AND active!=0 ");
            $ft->assign(array(
                'EXPIRE_DATE' => "<span class='expire_time' style='$display_expire_time'><span>".$tags['T.EXPIRE_ALREADY'].": click here to <a href=\"index.php?pag=profile_payment\">PAY NOW</a></span></span>",
            ));
        }
        else
        {
            $ft->assign(array(
                'EXPIRE_DATE' => "<span class='expire_time' style='$display_expire_time'><span>".$tags['T.EXPIRE']." ".$time_remained.": click here to <a href=\"index.php?pag=profile_payment\">PAY NOW</a></span></span>",
            ));
        }
        
        if((strtotime($get_time->f('expire_date'))<time()) && $glob['pag'] != "profile_payment") { $ft->assign('REDIRECT', page_redirect('index.php?pag=profile_payment&error=Your account has been expired!'));}
        else if((strtotime($get_time->f('expire_date'))<time()) && $get_time->f('active')==0 && $glob['pag'] != "profile_payment") { $ft->assign('REDIRECT', page_redirect('index.php?pag=profile_payment&error=Your account has been expired!'));}
        else { $ft->assign('REDIRECT', ''); }
        
        $ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
        $ft->parse('CONTENT','main');
        return $ft->fetch('CONTENT');
    }
    else {
        $expire_time = (strtotime($get_time->f('expire_date'))-time());
        
        $expire_days = intval(intval($expire_time) / (3600 * 24));
        $expire_hours = intval(intval($expire_time) / 3600);
        $expire_minutes = (intval(intval($expire_time) / 60) % 60);
        
        if($expire_days<1 && $expire_minutes<1)
        {
            $dbu->query("UPDATE trainer SET active=0 WHERE trainer_id=".$_SESSION[U_ID]." AND active!=0 ");
            $ft->assign(array(
                'EXPIRE_DATE' => "<span class='expire_time'><span>".$tags['T.EXPIRE_ALREADY']."</span></span>",
            ));
        }
        
        if((strtotime($get_time->f('expire_date'))<time()) && $glob['pag'] != "profile_payment") { $ft->assign('REDIRECT', page_redirect('index.php?pag=profile_payment&error=Your account has been expired!'));}
        else if((strtotime($get_time->f('expire_date'))<time()) && $get_time->f('active')==0 && $glob['pag'] != "profile_payment") { $ft->assign('REDIRECT', page_redirect('index.php?pag=profile_payment&error=Your account has been expired!'));}
        else { $ft->assign('REDIRECT', ''); }
        
        $ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
        $ft->parse('CONTENT','main');
        return $ft->fetch('CONTENT');
    }
}
else { return; }
?>