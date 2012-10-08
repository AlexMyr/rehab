<?php
error_reporting(E_ALL);

$is_not_hacked_yet =true;

$time_now = time();

/*
	require_once ('../config/config.php');
	require_once ('../misc/cls_mysql_db.php');
	require_once ('../misc/cms_front_lib.php');
	require_once ("../misc/security_lib.php");
	require_once ("../misc/stlib.php");
	require_once ('../classes/class.phpmailer.php');        
	include_once ("../classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
*/
	
    $root_path = dirname(dirname(__FILE__));
	require_once ($root_path.'/config/config.php');
	require_once ($root_path.'/misc/cls_mysql_db.php');
	require_once ($root_path.'/misc/cms_front_lib.php');
	require_once ($root_path."/misc/security_lib.php");
	require_once ($root_path."/misc/stlib.php");
	require_once ($root_path.'/classes/class.phpmailer.php');        
	include_once ($root_path."/classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

/*
send mails to trainers on trial time in order of days
30 - when joins in - this is made in the join page

AUTO MAILS:
	>	28	-	after	1	day
	>	26	-	after	3	days
	>	22	-	after	7	days
	>	15	-	after	14	days
	>	8	-	after	21	days
	>	3	-	after	26	days
	>	2	-	after	27	days
	>	1	-	after	28	days

*/

function get_sys_message($name, $lang)
{
    $_db=new mysql_db();
    $_db->query("select * from sys_message where name='".$name."' AND lang='".$lang."'");
    $_db->move_next();
    $msg['text']=$_db->f('text');
    $msg['from_email']=$_db->f('from_email');
    $msg['from_name']=$_db->f('from_name');
    $msg['subject']=$_db->f('subject');
    return $msg;
}
function send_mail($send_to_email,$send_to_name,$message_data, $type='trial')
{
        $ordermail = $send_to_email;
        $fromMail = $message_data['from_email']; 
        $replyMail = $message_data['from_email'];

		$body=$message_data['text'];

		if($type == 'never')
			$body=str_replace('[!EMAIL!]',$send_to_name, $body );
		
		$body=str_replace('[!NAME!]',$send_to_name, $body );
		$body = nl2br($body);
/*
		mail($ordermail,$message_data['subject'],$body);
		print_r($ordermail);
*/
                
        $mail = new PHPMailer();
		$mail->Mailer = 'sendmail';
		$mail->IsHTML(true);
        //$body             = file_get_contents('contents.html');
        //$body             = eregi_replace("[\]",'',$body);
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Host = SMTP_HOST; // sets the SMTP server
        $mail->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
        $mail->Username = SMTP_USERNAME; // SMTP account username
        $mail->Password = SMTP_PASSWORD; // SMTP account password

        $mail->SetFrom($fromMail, $fromMail);
        $mail->AddReplyTo($replyMail, $replyMail);

		$subject = $message_data['subject'];
		$mail->Subject = $subject;
        //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        //$mail->MsgHTML($body);
		$mail->Body = $body;

        $mail->AddAddress($ordermail, $send_to_name);
        $mail->Send();	

}
	
$dbu = new mysql_db();

$select = "select t.trainer_id as tid, t.email as email_contact, t.is_trial, t.is_clinic, t.lang, t.expire_date, t.create_date, t.username, thp.*, t.active from trainer t left join trainer_header_paper thp on t.trainer_id=thp.trainer_id where 1=1 and t.trainer_id IS NOT NULL";

$dbu->query($select);

$i = 0;

$ab = array();

while($dbu->move_next())
{
	//if($dbu->f('email_contact') != 'ole_gi@miralex.com.ua') continue;

	if($dbu->f('active')==1 || ($dbu->f('active')==2 && $dbu->f('expire_date')=='0000-00-00 00:00:00'))
	{
		$date_from_reg = ($time_now - strtotime($dbu->f('create_date')));
		$date_from_reg = intval(intval($date_from_reg) / (3600 * 24));
		
		$message_data=get_sys_message('never_'.$date_from_reg.'_days', $dbu->f('lang'));
		$send_to_name = $dbu->f('username');
		
		if($message_data['text']!=null) 
        {
			send_mail($send_to_email=$dbu->f('email_contact'), $send_to_name, $message_data, 'never');
            /* USED FOR THE CRON LOG FILE */
            $ab[$i]['msg_uid'] = $dbu->f('tid');
            $ab[$i]['msg_email'] = $dbu->f('email_contact');
            $ab[$i]['reg_date'] = $dbu->f('create_date');
            $ab[$i]['time'] = $time_now;
            $ab[$i]['name'] = 'never_'.$date_from_reg.'_days';
            $ab[$i]['msg_data'] = $message_data;
        }
		
		if($date_from_reg >= 49)
		{
			$dbu->query("delete from trainer where trainer_id=".$dbu->f('tid'));
			$dbu->query("delete from trainer_profile where trainer_id=".$dbu->f('tid'));
			$dbu->query("delete from trainer_header_paper where trainer_id=".$dbu->f('tid'));
			$dbu->query("delete from exercise_program_plan where trainer_id=".$dbu->f('tid'));
			$dbu->query("delete from exercise_plan_set where trainer_id=".$dbu->f('tid'));
			$dbu->query("delete from exercise_plan where trainer_id=".$dbu->f('tid'));
			$dbu->query("delete from exercise_notes where trainer_id=".$dbu->f('tid'));
			$dbu->query("delete from client where trainer_id=".$dbu->f('tid'));
		}
		
	}

    if($dbu->f('is_trial')!=0)
    {
        $expire_time = (strtotime($dbu->f('expire_date'))-$time_now);
        
        $expire_days = intval(intval($expire_time) / (3600 * 24));

		//hack for trial users which signed up before updating of payment plans
		if((strtotime($dbu->f('create_date')) <= mktime(0,0,0,7,4,2012)) && $expire_days>=0) continue;
		
        $expire_hours = intval(intval($expire_time) / 3600);
        $expire_minutes = (intval(intval($expire_time) / 60) % 60);
        if($expire_days>0 && $expire_days>1) $time_remained = "in <strong>".$expire_days." days</strong>"; 
        else if($expire_days>0 && $expire_days==1) $time_remained = "in <strong>".$expire_days." day</strong>"; 
        
        else if($expire_days<1 && $expire_minutes>0) $time_remained = "<strong>today</strong>"; 
//				echo "<pre>".$dbu->f('first_name')." ".$dbu->f('surname')." ".$dbu->f('email')." - expire ".$time_remained."</pre>";

		$message_data=get_sys_message('trial_'.$expire_days.'_days', $dbu->f('lang'));

		if($dbu->f('is_clinic') == 1)
		{
			$send_to_name=trim($dbu->f('company_name'));
		}
		else
		{
			$send_to_name=trim($dbu->f('first_name'));
		}
		
		
		if(!$send_to_name)
			$send_to_name = trim($dbu->f('first_name'));
		if(!$send_to_name)
			$send_to_name = trim($dbu->f('company_name'));
		if(!$send_to_name)
			$send_to_name = trim($dbu->f('surname'));
		if(!$send_to_name)
			$send_to_name = 'Client';
			

        if($message_data['text']!=null) 
        {
			send_mail($send_to_email=$dbu->f('email_contact'),$send_to_name,$message_data);
            /* USED FOR THE CRON LOG FILE */
            $ab[$i]['msg_uid'] = $dbu->f('trainer_id');
            $ab[$i]['msg_email'] = $dbu->f('email_contact');
            $ab[$i]['exp_date'] = $dbu->f('expire_date');
            $ab[$i]['exp_time'] = $expire_time;
            $ab[$i]['time'] = $time_now;
            $ab[$i]['name'] = 'trial_'.$expire_days.'_days';
            $ab[$i]['msg_data'] = $message_data;
        }
    }
    $i++;
}
	
print_r($ab);

?>