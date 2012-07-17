<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class contact
{
  var $dbu;
  
  function contact()
  {
    $this->dbu=new mysql_db;
	
	$root_path = dirname(dirname(__FILE__));
	//require_once ($root_path."/misc/security_lib.php");
	//require_once ($root_path."/misc/stlib.php");
	require_once ($root_path.'/classes/class.phpmailer.php');        
	include_once ($root_path."/classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
  }


  /****************************************************************
  * function add(&$ld)                                            *
  ****************************************************************/
  function add(&$ld)
  {
	if(!$this->add_validate($ld))
	{
	 return false;
	}
  
	$this->send_contact_us_notiffication_mail($ld);
	$ld['error']="Your message has been sent to the support team. We will contact you as soon as possible. Thank you.";
	$ld['pag']="cms";
	$ld['id']=130;
	$ld['p']='contact_us';
	return true;
  }

/****************************************************************
* function send_notiffication_mail(&$ld)                        *
****************************************************************/
  function send_contact_us_notiffication_mail(&$ld)
  {
	global $site_url,$script_path, $site_name,$contact_thankyou_page_id;
	
	$from="support@rehabmypatient.com";
	
	$body='New Contact From '.$site_name.' website

Contact Informations:
Name: '.$ld['name'].'
Email: '.$ld['email'].'
Phone Number: '.$ld['phone'].'
           
Message:
------------------------------------------------
'.$ld['comments'].'
';

	$body = nl2br($body);
	$mail = new PHPMailer();
	$mail->Mailer = 'sendmail';
	$mail->IsHTML(true);
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
	$mail->SMTPAuth = true; // enable SMTP authentication
	$mail->Host = SMTP_HOST; // sets the SMTP server
	$mail->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
	$mail->Username = SMTP_USERNAME; // SMTP account username
	$mail->Password = SMTP_PASSWORD; // SMTP account password

	$mail->SetFrom($ld['email'], $ld['email']);

	$subject = $message_data['subject'];
	$mail->Subject = $ld['subject'];
	$mail->Body = $body;

	$mail->AddAddress($to, $to);
	$mail->Send();
	
	//$mail=ADMIN_EMAIL;	
//	$mail="support@rehabmypatient.com";
//	$body='New Contact From '.$site_name.' website
//
//Contact Informations:
//Name: '.$ld['name'].'
//Email: '.$ld['email'].'
//Phone Number: '.$ld['phone'].'
//           
//Message:
//------------------------------------------------
//'.$ld['comments'].'
//';
//	
//	
//	$header.= "From: ".$ld['email']." \n";
//	$header.= "Content-Type: text\n";
//	$mail_subject=$ld['subject'];		   
//	@mail ( $mail , $mail_subject, $body , $header);
//	
	return true;
  }

/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function add_validate(&$ld)
{
    $is_ok=true;
    if(!$ld['name'])
    {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_NAME')."<br>";
            $is_ok=false;
    }
    if(!$ld['email'])
    {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_EMAIL')."<br>";
            $is_ok=false;
    }

    if($ld['email'] && !secure_email($ld['email']))
    {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.PROVIDE_EMAIL')."<br>";
            $is_ok=false;
    }
    
    if(!$ld['subject'])
    {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_SUBJECT')."<br>";
            $is_ok=false;
    }
    
    if(!$ld['comments'])
    {
            $ld['error'].=get_template_tag($ld['pag'], $ld['lang'], 'T.FILL_MESSAGE')."<br>";
            $is_ok=false;
    }
    return $is_ok;
}

}//end class
?>

