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
                $ld['pag']="contact_thankyou2";
				return true;
        }

/****************************************************************
* function send_notiffication_mail(&$ld)                        *
****************************************************************/
function send_contact_us_notiffication_mail(&$ld)
        {
 		   global $site_url, $site_name, $contact_thankyou_page_id;
           $mail=ADMIN_EMAIL;
           $body='New Contact From '.$site_name.' website

Subject: '.$ld['subject'].'

Contact Informations:
Name: '.$ld['name'].'
Email: '.$ld['email'].'
Phone: '.$ld['phone'].'
           
Message:
------------------------------------------------
'.$ld['comments'].'
';
           
           $body = str_replace("\'", "'", $body);
           $header.= "From: ".$ld['email']." \n";
		   $header.= "Content-Type: text\n";
		   $mail_subject=$ld['subject'];		   
           @mail ( $mail , $mail_subject, $body , $header);
           
           return true;
        }


/****************************************************************
* function property_contact(&$ld)                               *
****************************************************************/
function property_contact(&$ld)
        {
                if(!$this->property_contact_validate($ld))
                {
                     return false;
                }

                $this->send_property_contact_notiffication_mail($ld);
                $ld['pag']="contact_thankyou_small";
				return true;
        }

/****************************************************************
* function send_property_contact_notiffication_mail(&$ld)       *
****************************************************************/
function send_property_contact_notiffication_mail(&$ld)
        {
 		   global $site_url, $site_name;
 		   
$this->dbu->query("select member.first_name, member.last_name, member.email, property.id from member 
			 inner join property on property.member_id=member.member_id
			 where property.property_id = '".$ld['id']."'
");
$this->dbu->move_next();
 		   
           $mail=$this->dbu->f('email');
           $body='Property Information Request From '.$site_name.' website
           
Property of interest: Listing # '.$this->dbu->f('id').' 
'.$site_url.'index.php?pag=pr&id='.$ld['id'].'           

Subject: '.$ld['subject'].'           

Message:
------------------------------------------------
'.$ld['comments'].'
------------------------------------------------
                      
Contact Informations:
Name: '.$ld['name'].'
Email: '.$ld['email'].'
Phone: '.$ld['phone'].'
Address: '.$ld['address'].'
'.$ld['city'].', '.$ld['state'].', '.$ld['zip'].'

Contact Prefferences:                     
Contact Me: '.$ld['contact_me'].'
Best Time to Call Me: '.$ld['call_time'].'

I plan on buying a property: '.$ld['plan_on_buying'].'
';
           
           
 		   $body = str_replace("\'", "'", $body);          
           $header.= "From: ".$ld['email']." \n";
		   $header.= "Content-Type: text\n";
		   $mail_subject='Property Information Request #'.$this->dbu->f('id');		   
           @mail ( $mail , $mail_subject, $body , $header);
           
           return true;
        }
        

/****************************************************************
* function agent_contact(&$ld)                               *
****************************************************************/
function agent_contact(&$ld)
        {
                if(!$this->property_contact_validate($ld))
                {
                     return false;
                }

                $this->send_agent_contact_notiffication_mail($ld);
                $ld['pag']="contact_thankyou";
				return true;
        }

/****************************************************************
* function send_agent_contact_notiffication_mail(&$ld)       *
****************************************************************/
function send_agent_contact_notiffication_mail(&$ld)
        {
 		   global $site_url, $site_name;
 		   
$this->dbu->query("select member.first_name, member.last_name, member.email from member 
			 where member.member_id = '".$ld['id']."'
");
$this->dbu->move_next();
 		   
           $mail=$this->dbu->f('email');
           $body='New contact from '.$site_name.' website

Subject: '.$ld['subject'].'           

Message:
------------------------------------------------
'.$ld['comments'].'
------------------------------------------------
                      
Contact Informations:
Name: '.$ld['name'].'
Email: '.$ld['email'].'
Phone: '.$ld['phone'].'
Address: '.$ld['address'].'
'.$ld['city'].', '.$ld['state'].', '.$ld['zip'].'

Contact Prefferences:                     
Contact Me: '.$ld['contact_me'].'
Best Time to Call Me: '.$ld['call_time'].'

I plan on buying a property: '.$ld['plan_on_buying'].'
';
           
 		   $body = str_replace("\'", "'", $body);          
 		   $ld['subject'] = str_replace("\'", "'", $ld['subject']);          
           $header.= "From: ".$ld['email']." \n";
		   $header.= "Content-Type: text\n";
		   $mail_subject=$ld['subject'];		   
           @mail ( $mail , $mail_subject, $body , $header);
           
           return true;
        }
        

/****************************************************************
* function property_tell_friend(&$ld)                               *
****************************************************************/
function property_tell_friend(&$ld)
        {
                if(!$this->property_tell_friend_validate($ld))
                {
                     return false;
                }

                $this->send_property_tell_friend_notiffication_mail($ld);
                $ld['pag']="contact_thankyou_small";
				return true;
        }

/****************************************************************
* function send_property_tell_friend_notiffication_mail(&$ld)       *
****************************************************************/
function send_property_tell_friend_notiffication_mail(&$ld)
        {
 		   global $site_url, $site_name;
 		   
 		   
           $mail=$ld['receiver_email'];
           $body='
Hello  '.$ld['receiver_name'].'

'.$ld['sender_name'].' wanted to let you know about this ad on '.$site_name.' :
'.$site_url.'index.php?pag=pr&id='.$ld['id'].'           

------------------------------------------------
'.$ld['comments'].'
';
           
           
           $header.= "From: ".$ld['sender_email']." \n";
		   $header.= "Content-Type: text\n";
		   $mail_subject='I thought that you might be interested...';		   
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
                $ld['error'].="Please fill in the 'First Name' field."."<br>";
                $is_ok=false;
        }
        if(!$ld['email'])
        {
                $ld['error'].="Please fill in the 'Email' field."."<br>";
                $is_ok=false;
        }

        if($ld['email'] && !secure_email($ld['email']))
        {
                $ld['error'].="Please provide a valid email address."."<br>";
                $is_ok=false;
        }
         
        if(!$ld['subject'])
        {
                $ld['error'].="Please fill in the 'Subject' field."."<br>";
                $is_ok=false;
        }
         
        if(!$ld['comments'])
        {
                $ld['error'].="Please fill in the 'Message' field."."<br>";
                $is_ok=false;
        }
        return $is_ok;
}

/****************************************************************
* function property_contact_validate(&$ld)                      *
****************************************************************/

function property_contact_validate(&$ld)
{
        $is_ok=true;
        if(!$ld['name'])
        {
                $ld['error'].="Please enter Your Name."."<br>";
                $is_ok=false;
        }
        
        if(!$ld['email'])
        {
                $ld['error'].="Please fill in the 'Email' field."."<br>";
                $is_ok=false;
        }

        if($ld['email'] && !secure_email($ld['email']))
        {
                $ld['error'].="Please provide a valid Email address."."<br>";
                $is_ok=false;
        }
        
        return $is_ok;
}

/****************************************************************
* function property_tell_friend_validate(&$ld)                  *
****************************************************************/

function property_tell_friend_validate(&$ld)
{
	
        $is_ok=true;
        if(!$ld['sender_name'])
        {
                $ld['error'].="Please enter Your Name."."<br>";
                $is_ok=false;
        }
        if(!$ld['sender_email'])
        {
                $ld['error'].="Please enter Your Email Address."."<br>";
                $is_ok=false;
        }

        if($ld['sender_email'] && !secure_email($ld['sender_email']))
        {
                $ld['error'].="Please provide a valid Email Address."."<br>";
                $is_ok=false;
        }

        if(!$ld['receiver_name'])
        {
                $ld['error'].="Please enter Your Friend's Name."."<br>";
                $is_ok=false;
        }
        if(!$ld['receiver_email'])
        {
                $ld['error'].="Please enter Your Friend's Email Address."."<br>";
                $is_ok=false;
        }

        if($ld['receiver_email'] && !secure_email($ld['receiver_email']))
        {
                $ld['error'].="Please provide a valid Email Address."."<br>";
                $is_ok=false;
        }


        return $is_ok;
}


}//end class
?>

