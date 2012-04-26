<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/


include_once("module_config.php");
include_once("php/gen/startup.php");


$dbu=new mysql_db;
$dbu2=new mysql_db;

if(!$_SESSION[UID])
{
	return false;
}
else
{
	if($glob['campaign_start'] == 1)
	{
		$dbu->query("update settings set value='1' where constant_name='NL_CAMPAIGN'");
		$dbu->query("update settings set value='".$glob['newsletter_id']."' where constant_name='NL_ACTIVE'");
		$dbu->query("update nl_subscriber set emailed='0'");
		echo '
		<script language="javascript">
		<!-- 
		
		location.replace("send_emails.php?newsletter_id='.$glob['newsletter_id'].'");
		
		-->
		</script>
		'
		;		
	}
	elseif($glob['campaign_end'] == 1)
	{
		$dbu->query("update settings set value='0' where constant_name='NL_CAMPAIGN'");
		$dbu->query("update settings set value='0' where constant_name='NL_ACTIVE'");
		$dbu->query("update nl_subscriber set emailed='1'");
		echo '
		<script language="javascript">
		<!-- 
		
		location.replace("index_blank.php?pag=newsletter_view&newsletter_id='.$glob['newsletter_id'].'");
		
		-->
		</script>
		'
		;		
	}
	else 
	{
	
	$html_header = '<html>
	<head>
	<title>Sending Emails</title>
	<META HTTP-EQUIV=Refresh CONTENT="30"> 
	<link rel="stylesheet" href="layout.css" type="text/css">
	</head>
	
	<body>
	<a href="send_emails.php?newsletter_id='.$glob['newsletter_id'].'&campaign_end=1" class="RedBoldLink"> >> Stop Emails << </a><br><br>
	<font class="verdanaRed11">Sending Emails :</font><br><font class="verdana10">';
	echo $html_header;
	
	$dbu->query("select * from nl_newsletter where newsletter_id='".NL_ACTIVE."'");
	$dbu->move_next();
	$mode=$dbu->f('mode');
	// HTML Emails
	if($mode == 1)
	{
		$header="";
		$header = "MIME-Version: 1.0\r\n";
		$header.= "Content-Type: text/html \r\n";
		$header.= "From: ".$dbu->f('from_name')." <".$dbu->f('from_email')."> \r\n";
		$mail_subject=$dbu->f('subject');
		$body = $dbu->f('body');	
	
	
	}
	// Plain Text Emails
	elseif($mode==2)
	{
		$header="";
		$header = "MIME-Version: 1.0\r\n";
		$header.= "Content-Type: text \r\n";
		$header.= "From: ".$dbu->f('from_name')." <".$dbu->f('from_email')."> \r\n";
		$mail_subject=$dbu->f('subject');
		$body=get_safe_text($dbu->f('body'));
	
	}  
	
	$original_body = $body;
	
	$dbu->query("select * from nl_subscriber where emailed='0' and active='1'");
	$e_s = NL_EMAILS_PER_SESSION / 2;
	while($dbu->move_next() && $e_s <= NL_EMAILS_PER_SESSION)
	{
		$body = $original_body;
		
		$receiver_email=$dbu->f('email');
		$subscriber_id=$dbu->f('subscriber_id');
		
		$first_name=$dbu->f('first_name');
		$last_name=$dbu->f('last_name');
		$entire_name = $first_name.' '.$last_name;
		$unsubscribe_link = $site_url.'nlu.php?id='.$subscriber_id;
		
		
		// prepare body
		$body=str_replace('[!FIRST_NAME!]',$first_name, $body);
		$body=str_replace('[!LAST_NAME!]',$last_name, $body);
		$body=str_replace('[!EMAIL!]',$receiver_email, $body);
		$body=str_replace('[!NAME!]',$entire_name, $body);
		$body=str_replace('[!UNSUBSCRIBE_LINK!]',$unsubscribe_link, $body);
		$body=str_replace('%5B%21UNSUBSCRIBE_LINK%21%5D',$unsubscribe_link, $body);
		
		echo "Sending To ".$entire_name.' - '.$receiver_email.' ...';
		
	    if( @mail ( $receiver_email , $mail_subject, $body , $header))
	    {
	          $dbu2->query("update nl_subscriber set emailed='1' where subscriber_id ='".$subscriber_id."'");
	          echo '<font color="#000000"> Sent</font><br>';
	    }
	    else
	    {
	    	echo '<font color="#FF0000"> Failed</font><br>';
	    }
		$e_s++;
		
	}
	
	// If no more emails to send out //
	if ($e_s == 0)
	{
		echo '
		<script language="javascript">
		<!-- 
		
		location.replace("send_emails.php?newsletter_id='.$glob['newsletter_id'].'&campaign_end=1");
		
		-->
		</script>
		'
		;		
	}

	
	$footer='</font>
	<br>
	</body>
	</html>
	';
	echo  $footer;   
	}

}
?>