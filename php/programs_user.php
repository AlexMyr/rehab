<?php
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "programs_user.html"));

$ft->define_dynamic('client_line','main');

$max_rows = '';
$l_r = 12;

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

$dbu->query("select * from programs as p left join programs_translate_".$_COOKIE['language']." as pt on p.programs_id = pt.programs_id where p.owner=".$_SESSION[U_ID]." ORDER BY p.sort_order ASC ");

$i=0;

$max_rows=$dbu->records_count();
$dbu->move_to($offset*$l_r);
while ($dbu->move_next()&&$i<$l_r)
{
		$ft->assign(array(
			'PROGRAM_ID'=>$dbu->f('programs_id'),
			'PROGRAM_NAME'=>stripslashes($dbu->f('programs_title')),
			'PROGRAM_DESC'=>stripslashes($dbu->f('description')),
		));
	$ft->parse('CLIENT_LINE_OUT','.client_line');
	$i++;
}


/// paginate here
$start = $offset;
$end = ceil($max_rows/$l_r);
$link = '';
if($end>1)
{
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

$ft->assign('CSS_PAGE', $glob['pag']);

$ft->assign('FIRST_NAME', $glob['first_name']);
$ft->assign('SURNAME', $glob['surname']);
$ft->assign('EMAIL', $glob['email']);
//$ft->assign('IMAGE_TYPE', $glob['print_image_type']);
$ft->assign('CLIENT_NOTE', $glob['client_note']);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>