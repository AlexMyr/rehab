<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "exercise_history.html"));

$ft->define_dynamic('client_line','main');
$dbu = new mysql_db();

if(isset($_SESSION[U_ID]))
{
  $tmp_table_name = "exercis_hist_".$_SESSION[U_ID];
  $dbu->query("
			  create table $tmp_table_name
			  as
			  select * from client_history
			  where trainer_id = ".$_SESSION[U_ID]."
			  order by date desc
			  limit 0, 200;
			  ");
  $dbu->query("
			  delete from client_history
			  where trainer_id = ".$_SESSION[U_ID].";
			  ");
  $dbu->query("
			  insert into client_history select * from $tmp_table_name;
			  ");
  $dbu->query("
			  drop table $tmp_table_name;
			  ");

  $dbu->query("select * from client_history ch left join client c on c.client_id = ch.client_id where ch.trainer_id=".$_SESSION[U_ID]." order by ch.date desc");
  
  $i=0;
  
  $ft->assign('CLEAR_HISTORY_LINK', 'index.php?pag=exercise_history&act=client-clear_exercise_history&trainer_id='.$_SESSION[U_ID]);
  
  while ($dbu->move_next())
  {
	$ft->assign(array(
	  'CLIENT_ID'=>$dbu->f('client_id'),
	  'CLIENT_NAME'=> $dbu->f('client_name') ?  stripslashes($dbu->f('client_name')) : stripslashes($dbu->f('first_name')." ".$dbu->f('surname')),
	  'ACTION'=>htmlentities($dbu->f('action')),
	  'DATE'=>date('d/n/Y', $dbu->f('date')),
	));
	$ft->parse('CLIENT_LINE_OUT','.client_line');
	$i++;
  }
}

$ft->assign('CSS_PAGE', $glob['pag']);

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