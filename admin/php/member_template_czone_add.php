<?php

/************************************************************************

* @Author: Tinu Coman

***********************************************************************/



$dbu=new mysql_db;

$dbu2=new mysql_db;



$ft=new ft(ADMIN_PATH.MODULE."templates/");

$ft->define(array('main' => "member_template_czone_add.html"));



if(!is_numeric($glob['template_czone_id']))

{

	$page_title="Add Content Zone for Member Module Template";

	$next_function='member_template_czone-add';

	

    if(!is_numeric($glob['mode']))

    {

    	$glob['mode']=1;

    }

	

    $ft->assign(array(

                        "TAG"               =>        $glob['tag'],

                        "NAME"              =>        $glob['name'],

                        "MODE"              =>        build_content_input_mode_list($glob['mode']),

                        "MODE_MESSAGE"      =>        get_content_input_mode_message($glob['mode'])

                     )

                );



    $params['cols']=80;

    $params['rows']=16;

    $ft->assign('CONTENT_INPUT_AREA',get_content_input_area($glob['mode'], $glob['content'], 'content',$params));

    

}

else

{

    $page_title="Edit Content Zone for Member Module Template";

    $next_function='member_template_czone-update';

    $dbu->query("select * from trainer_dashboard_template_czone 

    			 where template_czone_id='".$glob['template_czone_id']."'");

    $dbu->move_next();

		

    $ft->assign(array(

                        "TEMPLATE_CZONE_ID" =>       $glob['template_czone_id'],

                        "TAG"              =>        $dbu->gf('tag'),

                        "NAME"             =>        $dbu->gf('name'),

                        "MODE"             =>        build_content_input_mode_list($dbu->gf('mode')),

                        "MODE_MESSAGE"     =>        get_content_input_mode_message($dbu->gf('mode')),

                     )

                );

                

    $params['cols']=80;

    $params['rows']=16;

    $ft->assign('CONTENT_INPUT_AREA',get_content_input_area($dbu->gf('mode'), $dbu->f('content'), 'content',$params));

}



$ft->assign('PAGE_TITLE',$page_title);

$ft->assign('NEXT_FUNCTION',$next_function);

$ft->assign('TEMPLATE_CZONE_ID',$glob['template_czone_id']);

$ft->assign('MESSAGE',$glob['error']);



$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');



?>