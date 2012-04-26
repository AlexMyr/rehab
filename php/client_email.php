<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "client.html"));
//$ft->assign('MESSAGE', get_error($glob['error']));

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}
//$page_title='Login Member';
//$next_function ='auth-login';

$max_rows = '';
$l_r = ROW_PER_PAGE;
//$l_r = 2;

$dbu = new mysql_db();

if(($glob['ofs']) || (is_numeric($glob['ofs'])))
{
$glob['offset']=$glob['ofs'];
}
if((!$glob['offset']) || (!is_numeric($glob['offset'])))
{
        $offset=0;
}
else
{
    $offset=$glob['offset'];
    $ft->assign('OFFSET',$glob['offset']);
}

//$dbu->query("select name from cms_menu where menu_id=".$glob['menu_id']);

$select = "select client.* from client where 1=1 ";

if(!empty($glob['client_id']) && is_numeric($glob['client_id'])) $select .= "AND client.client_id=".$glob['client_id']." ";

$ft->assign('CLIENT_ID', $glob['client_id']);

$dbu->query($select);

$i = 0;

while($dbu->move_next())
{
		$ft->assign(array(
			'FIRST_NAME'=>$dbu->f('first_name'),
			'SURNAME'=>$dbu->f('surname'),
			'CLIENT_NAME'=>$dbu->f('first_name')." ".$dbu->f('surname'),
			'EMAIL'=>$dbu->f('email'),
			'CLIENT_NOTE'=>$dbu->f('client_note'),
		));
		$i++;
}

$ft->assign('CSS_PAGE', $glob['pag']);



$ft->define_dynamic('client_record_line','main');

$programs = $dbu->query("select exercise_plan.* from exercise_plan where exercise_plan.trainer_id=".$_SESSION[U_ID]." AND exercise_plan.client_id=".$glob['client_id']." ");

$i=0;

$max_rows=$programs->records_count();
$programs->move_to($offset*$l_r);
while ($programs->next()&&$i<$l_r)
{
//	$dbu->query("select count(exercise_plan_id) as cnt from exercise_plan where trainer_id=".$_SESSION[U_ID]." AND client_id=".$dbu->f('client_id')." ");
//	$dbu->move_next();
		$ft->assign(array(
			'EXERCISE_PLAN_ID'=>$programs->f('exercise_plan_id'),
			'CREATE_DATE'=>date('D jS M Y, h.ia',strtotime($programs->f('date_created'))),
			'MODIFY_DATE'=>date('D jS M Y, h.ia',strtotime($programs->f('date_modified'))),
		));
	$ft->parse('CLIENT_RECORD_LINE_OUT','.client_record_line');
	$i++;
}
if ($i==0) {
//	return '';
	$glob['error'] = $tags['T.NOT_FOUND'];
}
/// paginate here
$start = $offset;
$end = ceil($max_rows/$l_r);
$link = '';
if($end<=5){
    //if there are less then 5 pages then we go about building a normal pagination
    for ($i = 0; $i < $end; $i++){
        $page = $i+1;
        $class = $page == $start+1 ? 'class="moreBtn current"' : 'class="moreBtn"';
        $link .= <<<HTML
<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page}</span></a></li>
HTML;
    }
}else{
    if($start == 0 || $start <3){
        for ($i = 0; $i < 5; $i++){
            $page = $i+1;
            $class = $page == $start+1 ? 'class="moreBtn current"' : 'class="moreBtn"';
            $link .= <<<HTML
<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page}</span></a></li>
HTML;
        }
    }elseif ($start+2 >= $end-1){
        //we are close to the end
        for ($i = $end-5; $i < $end; $i++){
            $page = $i+1;
            $class = $page == $start+1 ? 'class="moreBtn current"' : 'class="moreBtn"';
            $link .= <<<HTML
<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page}</span></a></li>
HTML;
        }
    }else{
        for ($i = $start-2; $i < $start; $i++){
            $page = $i+1;
            $link .= <<<HTML
<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}"><span>{$page}</span></a></li>
HTML;
        }
        $page = $start+1;
        $class = $page == $start+1 ? 'class="moreBtn current"' : 'class="moreBtn"';
        $link .= <<<HTML
<li><a href="index.php?pag={$glob['pag']}&offset={$start}{$arguments}" {$class}><span>{$page}</span></a></li>
HTML;
        for ($i = $start+1; $i < $start+3; $i++){
            $page = $i+1;
            $link .= <<<HTML
<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}"><span>{$page}</span></a></li>
HTML;
        }
    }
}
$ft->assign(array(
    'PAGG' => $link,
    'PAG' => $glob['pag'],
    'OFFSET' => $offset
));

if($offset > 0)
{
     $ft->assign('CSS_BACKLINK', 'prev');
     $ft->assign('BACKLINK',"index.php?pag=".$glob['pag']."&offset=".($offset-1).$arguments);
}
else
{
     $ft->assign('CSS_BACKLINK', 'prev displayNone');
     $ft->assign('BACKLINK','#');
}
if($offset < $end-1)
{
     $ft->assign('CSS_NEXTLINK', 'next');
     $ft->assign('NEXTLINK',"index.php?pag=".$glob['pag']."&offset=".($offset+1).$arguments);
}
else
{
     $ft->assign('CSS_NEXTLINK', 'next displayNone');
     $ft->assign('NEXTLINK','#');
}
if($offset < $end-1) $ft->assign('CSS_LAST_LINK', 'last');
else $ft->assign('CSS_LAST_LINK', 'last displayNone');
$ft->assign('LAST_LINK',"index.php?pag=".$glob['pag']."&last=1&offset=".($end-1).$arguments); 
/// end paginate

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>