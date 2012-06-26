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
	//$mail=ADMIN_EMAIL;
	$mail="support@rehabmypatient.com";
	$body='New Contact From '.$site_name.' website

Contact Informations:
Name: '.$ld['name'].'
Email: '.$ld['email'].'
Phone Number: '.$ld['phone'].'
           
Message:
------------------------------------------------
'.$ld['comments'].'
';
	
	
	$header.= "From: ".$ld['email']." \n";
	$header.= "Content-Type: text\n";
	$mail_subject=$ld['subject'];		   
	@mail ( $mail , $mail_subject, $body , $header);
	
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

