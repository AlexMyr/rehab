<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "login.html"));

//$page_title='Login Member';
//$next_function ='auth-login';
session_start();
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
if($user && isset($_SESSION['fb_login_rmp']) && $_SESSION['fb_login_rmp'])
{
	//check existing
	$dbu->query("select * from trainer where fb_id = ".$user);
	$is_user_in_db = $dbu->move_next();
	if($is_user_in_db && $dbu->f('active')!=0)
	{
		header("Location: /index.php?act=auth-login&fb_id=".$user);
		exit;
	}
	//elseif($is_user_in_db && $dbu->f('active')==0)
	//{
	//	$_SESSION['fb_login_rmp'] = 0;
	//	header("Location: /index.php?&pag=login&success=false&error=".urlencode('Username was banned for a reason. Please contact support for more details!'));
	//	exit;
	//}
	else
	{

		if(isset($user_profile['email']) && $user_profile['email'])
		{
			$dbu->query("SELECT trainer_id FROM trainer WHERE username = '{$user_profile['email']}' AND (fb_id IS NULL OR fb_id='')");
			if($dbu->move_next())
			{
				$dbu->query("
							UPDATE 
										trainer 
							SET 
										fb_id = '".$user_profile['id']."'
							WHERE trainer_id = '".$dbu->f('trainer_id')."'
							");
				header("Location: /index.php?act=auth-login&pag=login&fb_id=".$user_profile['id']);
				exit;
			}
			else
			{
			
				$dbu->query("
								INSERT INTO 
										trainer 
							SET 
										username='".$user_profile['email']."', 
										email='".$user_profile['email']."',
										first_name='".$user_profile['first_name']."',
										surname='".$user_profile['last_name']."',
										password='', 
										create_date=NOW(), 
										is_trial='1', 
										expire_date='', 
										active = '1',
										fb_id = '".$user_profile['id']."'
							");
			
				header("Location: /index.php?act=auth-login&pag=login&fb_id=".$user_profile['id']);
				exit;
			}
		}
		else
		{
			$dbu->query("
							INSERT INTO 
									trainer 
						SET 
									username='', 
									email='',
									first_name='".$user_profile['first_name']."',
									surname='".$user_profile['last_name']."',
									password='', 
									create_date=NOW(), 
									is_trial='1', 
									expire_date='', 
									active = '1',
									fb_id = '".$user_profile['id']."'
						");
			header("Location: /index.php?act=auth-login&pag=login&fb_id=".$user_profile['id']);
			exit;
		}
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