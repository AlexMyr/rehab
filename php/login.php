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
  'appId'  => '264894086933472',
  'secret' => '9e61aeb9b7632060cc834d6ad4b106a3',
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

if($user)
{
		
	//check existing
	$dbu->query("select * from trainer where fb_id = ".$user);
	if($dbu->move_next())
	{
		
		header("Location: http://rehabmypatient.com/index.php?act=auth-login&pag=login&fb_id=".$user);
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
		header("Location: http://rehabmypatient.com/index.php?act=auth-login&pag=login&fb_id=".$user);
		exit;
	}
}
else
{
	$fb_button = "<fb:login-button></fb:login-button>
					<div id='fb-root'></div>
					<script>
					  window.fbAsyncInit = function() {
						FB.init({
						  appId: '".$facebook->getAppID()."',
						  cookie: true,
						  xfbml: true,
						  oauth: true
						});
						FB.Event.subscribe('auth.login', function(response) {
						  window.location.reload();
						});
						FB.Event.subscribe('auth.logout', function(response) {
						  window.location.reload();
						});
					  };
					  (function() {
						var e = document.createElement('script'); e.async = true;
						e.src = document.location.protocol +
						  '//connect.facebook.net/en_US/all.js';
						document.getElementById('fb-root').appendChild(e);
					  }());
					</script>";
}
$ft->assign('FB_LOGIN', $fb_button);
/*fb section */

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');
?>