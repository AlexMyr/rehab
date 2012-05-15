<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "dashboard.html"));

$ft->define_dynamic('client_line','main');

$max_rows = '';
$l_r = ROW_PER_PAGE;
//$l_r = 2;

$dbu = new mysql_db();

//if(($glob['ofs']) || (is_numeric($glob['ofs'])))
//{
//$glob['offset']=$glob['ofs'];
//}
//if((!$glob['offset']) || (!is_numeric($glob['offset'])))
//{
//        $offset=0;
//}
//else
//{
//    $offset=$glob['offset'];
//    $ft->assign('OFFSET',$glob['offset']);
//}
$ft->assign('IMAGE_TYPE',build_print_image_type_list(1));

$chk_trial = $dbu->field("SELECT is_trial FROM trainer WHERE trainer_id='".$_SESSION[U_ID]."'");

if(isset($glob['fchar']) && $glob['fchar'] != 'all')
{
		//var_dump("select client.* from client where client.trainer_id=".$_SESSION[U_ID]." and SUBSTRING(surname, 1, 1) = '".$glob['fchar']."' ORDER BY surname ASC ");exit;
		$dbu->query("select client.* from client where client.trainer_id=".$_SESSION[U_ID]." and SUBSTRING(surname, 1, 1) = '".$glob['fchar']."' ORDER BY surname ASC ");
}
else
{
		$dbu->query("select client.* from client where client.trainer_id=".$_SESSION[U_ID]." ORDER BY surname ASC ");
}


/*
$dbu->query("
				SELECT 
					client.*, COUNT(exercise_plan_id) AS cnt
				FROM
						client 
					LEFT JOIN 
						exercise_plan ON client.trainer_id=exercise_plan.trainer_id
				WHERE
					client.trainer_id=".$_SESSION[U_ID]." ");
*/

$i=0;

$max_rows=$dbu->records_count();
$dbu->move_to($offset*$l_r);
while ($dbu->move_next()/*&&$i<$l_r*/)
{
//	$dbu->query("select count(exercise_plan_id) as cnt from exercise_plan where trainer_id=".$_SESSION[U_ID]." AND client_id=".$dbu->f('client_id')." ");
//	$dbu->move_next();
		$ft->assign(array(
			'CLIENT_ID'=>$dbu->f('client_id'),
			'CLIENT_NAME'=>$dbu->f('surname').", ".$dbu->f('first_name'),
//			'EXERCISES_NR'=>$dbu->f('cnt'),
			'EXERCISES_NR'=>count_exercise($_SESSION[U_ID],$dbu->f('client_id')),
			'ACTIVITY_DATE'=>date('D jS M Y',strtotime($dbu->f('modify_date'))),
			'SHOW_LIMIT_ERROR'=>(count_exercise($_SESSION[U_ID],$dbu->f('client_id'))>4 && $chk_trial) ? 'showLimitError' : '',
		));
	$ft->parse('CLIENT_LINE_OUT','.client_line');
	$i++;
}
if ($i==0) {
//	return '';
//	$glob['error'] = 'You don\'t have any client yet. Please add a new client first.';
}

/// paginate here

//get first chars
$firstCharArray = array();
$dbu->query("select SUBSTRING(surname, 1, 1) as sstr from client where client.trainer_id=".$_SESSION[U_ID]." ORDER BY surname ASC");
while($dbu->move_next())
{
		$firstCharArray[] = strtolower($dbu->f('sstr'));
}
$firstCharArray = array_unique($firstCharArray);
$firstCharArray[] = 'all';

foreach($firstCharArray as $fChar)
{
		$page = $dbu->f('surname');
                $class = 'class="moreBtn"';
                $link .= <<<HTML
<li><a href="index.php?pag={$glob['pag']}&fchar={$fChar}" {$class}><span>{$fChar}</span></a></li>
HTML;
}

//$start = $offset;
//$end = ceil($max_rows/$l_r);
//$link = '';
//if($end>1)
//{
//    $dbu->query("select client.* from client where client.trainer_id=".$_SESSION[U_ID]." ORDER BY surname ASC ");
//	
//	
//   
//    if($end<=5){
//        //if there are less then 5 pages then we go about building a normal pagination
//        for ($i = 0; $i < $end; $i++){
//            //$page = $i+1;
//            $dbu->move_to($i*$l_r);
//            $dbu->move_next();
//            $page = $dbu->f('surname');
//            $class = $page == $start+1 ? 'class="moreBtn current"' : 'class="moreBtn"';
//            $link .= <<<HTML
//<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page[0]}</span></a></li>
//HTML;
//        }
//    }else{
//        if($start == 0 || $start <3){
//            for ($i = 0; $i < 5; $i++){
//                //$page = $i+1;
//                $dbu->move_to($i*$l_r);
//                $dbu->move_next();
//                $page = $dbu->f('surname');
//                $class = $i == $offset ? 'class="moreBtn current"' : 'class="moreBtn"';
//                $link .= <<<HTML
//<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page[0]}</span></a></li>
//HTML;
//            }
//        }elseif ($start+2 >= $end-1){
//            //we are close to the end
//            for ($i = $end-5; $i < $end; $i++){
//                //$page = $i+1;
//                $dbu->move_to($i*$l_r);
//                $dbu->move_next();
//                $page = $dbu->f('surname');
//                $class = $i == $offset ? 'class="moreBtn current"' : 'class="moreBtn"';
//                $link .= <<<HTML
//<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page[0]}</span></a></li>
//HTML;
//            }
//        }else{
//            for ($i = $start-2; $i < $start; $i++){
//                //$page = $i+1;
//                $dbu->move_to($i*$l_r);
//                $dbu->move_next();
//                $page = $dbu->f('surname');
//                $class = $i == $offset ? 'class="moreBtn current"' : 'class="moreBtn"';
//                $link .= <<<HTML
//<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page[0]}</span></a></li>
//HTML;
//            }
//            //$page = $start+1;
//            $dbu->move_to($i*$l_r);
//            $dbu->move_next();
//            $page = $dbu->f('surname');
//            $class = $i == $offset ? 'class="moreBtn current"' : 'class="moreBtn"';
//            $link .= <<<HTML
//<li><a href="index.php?pag={$glob['pag']}&offset={$start}{$arguments}" {$class}><span>{$page[0]}</span></a></li>
//HTML;
//            for ($i = $start+1; $i < $start+3; $i++){
//                //$page = $i+1;
//                $dbu->move_to($i*$l_r);
//                $dbu->move_next();
//                $page = $dbu->f('surname');
//                $class = $i == $offset ? 'class="moreBtn current"' : 'class="moreBtn"';
//                $link .= <<<HTML
//<li><a href="index.php?pag={$glob['pag']}&offset={$i}{$arguments}" {$class}><span>{$page[0]}</span></a></li>
//HTML;
//            }
//        }
//    }
//}

$ft->assign(array(
    'PAGG' => $link,
    'PAG' => $glob['pag'],
    'OFFSET' => $offset,
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


//$ft->assign('ALL_LINK',"index.php?pag=".$glob['pag']."&fchar=all");
//hide next/last link
$ft->assign('CSS_LAST_LINK', 'last displayNone');
$ft->assign('CSS_NEXTLINK', 'next displayNone');

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