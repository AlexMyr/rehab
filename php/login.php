<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "login.html"));

//$page_title='Login Member';
//$next_function ='auth-login';

$dbu = new mysql_db();

//$dbu->query("select name from cms_menu where menu_id=".$glob['menu_id']);

$ft->assign('CSS_PAGE', $glob['pag']);

if($glob['username']) 
	{
		$ft->assign('UNAME', $glob['username']);	
	}
if($glob['join_email']) 
	{
		$ft->assign('JOIN_MAIL', $glob['join_email']);	
	}

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

/*fb section */
require 'fb/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '140136112789193',
  'secret' => 'dfa5828ecca71050bfd974f659003a66',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    $user = null;
  }
}

if($user && isset($_SESSION['fb_login_rmp']) && $_SESSION['fb_login_rmp'])
{
	
	//check existing
	$dbu->query("select * from trainer where fb_id = ".$user);
	if($dbu->move_next())
	{
		
		header("Location: /index.php?act=auth-login&pag=login&fb_id=".$user);
		exit;
		//$dbu->query("select * from trainer where fb_id = ".$user_profile['id']);
	}
	else
	{
			$dbu->query("
									INSERT INTO 
												trainer 
									SET 
												username='".$user_profile['email']."', 
												email='".$user_profile['email']."',
												password='', 
												create_date=NOW(), 
												is_trial='1', 
												expire_date='', 
												active = '1',
												fb_id = '".$user_profile['id']."'
									");
		header("Location: /index.php?act=auth-login&pag=login&fb_id=".$user);
		exit;
	}
}
else
{
	$fb_button = "<a href='index.php?act=auth-login_fb&pag=login'><img src='/img/fb_login_button.png' alt='fb login' /></a>";
	$ft->assign('FB_LOGIN', $fb_button);
}

/*fb section */

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>