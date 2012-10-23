<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/
unset($ftm);

if(!isset($glob['p']))
    $glob['p'] = 'home';

$glob['web_page_name'] = $glob['p'].'_'.strtoupper($glob['lang']);
$dbu=new mysql_db;
$dbu->query("select cms_web_page.*, cms_template.file_name from cms_web_page
			 inner join cms_template on cms_web_page.template_id=cms_template.template_id
			 where cms_web_page.name='".$glob['web_page_name']."'
			");
if(!$dbu->move_next())
{
	$dbu->query("select cms_web_page.*, cms_template.file_name from cms_web_page
			 inner join cms_template on cms_web_page.template_id=cms_template.template_id
			 order by date DESC
			");
	if(!$dbu->move_next())
	{
		echo " ";
		exit;
	}
}

$glob['web_page_id'] = $dbu->f('web_page_id');
$template_file_name=$dbu->f('file_name');
$template_id=$dbu->f('template_id');
$page_meta_title=$dbu->f('title');
$page_meta_keywords=$dbu->f('keywords');
$page_meta_description=$dbu->f('description');

$ftm=new ft("");
$ftm->define(array('main'=>$template_file_name));

$dbu->query("select * from cms_template_czone where template_id='".$template_id."'");
while($dbu->move_next())
{
	$template_tags[$dbu->f('template_czone_id')]=$dbu->f('tag');
}

if($template_tags)
foreach ($template_tags as $template_czone_id => $template_czone_tag)
{
	$tag_content='';
	$dbu->query("select * from cms_web_page_content
				 where template_czone_id='".$template_czone_id."' and web_page_id='".$glob['web_page_id']."'
				 order by sort_order");

	while($dbu->move_next())
	{
		$article_parms['date']=date("m / d / Y", $dbu->f('date'));
		$article_parms['content_template_id']=$dbu->f('content_template_id');
		$article_parms['title']=$dbu->f('title');
		$article_parms['subtitle']=$dbu->f('subtitle');
		$article_parms['headline']=$dbu->f('headline');
		$article_parms['content']=$dbu->f('content');
		$article_parms['mode']=$dbu->f('mode');
		$article_parms['banner'] = parseCMSTag('[!BANNER!]');
		$tag_content.=get_content_article($article_parms);
		unset ($article_parms);
	}
	//get tags from content
	$cms_tag_array=get_cms_tags_from_content($tag_content);
	//****Replacing the CMS tags with objects
	if($cms_tag_array)
        foreach ( $cms_tag_array as $key => $cms_tag_params)
        {
            //if($cms_tag_params['tag'] == '!MAIN_MENU!') $cms_tag_params['tag'] = 'LOGGED_IN_MENU';
            $tag_content=str_replace($cms_tag_params['tag'], get_cms_tag_content($cms_tag_params), $tag_content);
        }
	$ftm->assign($template_czone_tag, $tag_content);
}
//exit;
$ftm->assign('META_TITLE', $page_meta_title);
$ftm->assign('META_KEYWORDS', $page_meta_keywords);
$ftm->assign('META_DESCRIPTION', $page_meta_description);

//get date of css/js files modified
$css_stat = stat(dirname(dirname(__FILE__))."/css/style.css");
$js_stat = stat(dirname(dirname(__FILE__))."/js/init_dom_private.js");

$ftm->assign('CSS_MODIFY', $css_stat['mtime']);
$ftm->assign('JS_MODIFY', $css_stat['mtime']);

if($_SESSION[U_ID]) $ftm->assign('DISPLAY_LOGIN_BLOCK', 'display:none;');
else $ftm->assign('T.MY_PROGRAMMES', '');

$my_programs = get_template_tag('programs', $glob['lang'], 'T.MY_PROGRAMMES');
$username = get_template_tag('login', $glob['lang'], 'T.USERNAME');
$pass = get_template_tag('login', $glob['lang'], 'T.PASS');
$forgot = get_template_tag('login', $glob['lang'], 'T.FORGOT');
//$my_programs = get_template_tag('login', $glob['lang'], 'T.');
$ftm->assign('T.MY_PROGRAMMES', $my_programs);
$ftm->assign('T.USERNAME', $username);
$ftm->assign('T.PASS', $pass);
$ftm->assign('T.FORGOT', $forgot);

$ftm->assign('BOTTOM_INCLUDES', $bottom_includes);
$ftm->parse('CONTENT','main');
$ftm->ft_print('CONTENT');

if($debug)
	{
	   require($script_path."misc/debug.php");
	}

exit();
?>
