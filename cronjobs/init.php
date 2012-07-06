<?php
//error_reporting(0);

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
function send_mail($send_to_email,$send_to_name,$message_data)
{
        $ordermail = $send_to_email;
        $fromMail = $message_data['from_email']; 
        $replyMail = $message_data['from_email'];

		$body=$message_data['text'];

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

$select = "select trainer.* from trainer where 1=1";

$dbu->query($select);

$i = 0;

$ab = array();

while($dbu->move_next())
{

    if($dbu->f('is_trial')!=0)
    {
        $expire_time = (strtotime($dbu->f('expire_date'))-$time_now);
        
        $expire_days = intval(intval($expire_time) / (3600 * 24));
        $expire_hours = intval(intval($expire_time) / 3600);
        $expire_minutes = (intval(intval($expire_time) / 60) % 60);
        if($expire_days>0 && $expire_days>1) $time_remained = "in <strong>".$expire_days." days</strong>"; 
        else if($expire_days>0 && $expire_days==1) $time_remained = "in <strong>".$expire_days." day</strong>"; 
        
        else if($expire_days<1 && $expire_minutes>0) $time_remained = "<strong>today</strong>"; 
//				echo "<pre>".$dbu->f('first_name')." ".$dbu->f('surname')." ".$dbu->f('email')." - expire ".$time_remained."</pre>";

        $message_data=get_sys_message('trial_'.$expire_days.'_days', $dbu->f('lang'));
        
        if($message_data['text']!=null) 
        {
			send_mail($send_to_email=$dbu->f('email'),$send_to_name=$dbu->f('first_name').' '.$dbu->f('surname'),$message_data);
            /* USED FOR THE CRON LOG FILE */
            $ab[$i]['msg_uid'] = $dbu->f('trainer_id');
            $ab[$i]['msg_email'] = $dbu->f('email');
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