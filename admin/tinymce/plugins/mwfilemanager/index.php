<?php
include_once('../../../../misc/cls_ft.php');
include_once('../../../../config/config.php');

foreach($HTTP_GET_VARS as $key => $value)
    {
        $glob[$key]=$value;
    }


foreach($HTTP_POST_VARS as $key => $value)
    {
        $glob[$key]=$value;
    }

	require_once('cls/cls_managament.php');
	$manager = new managament();
	switch ($glob['act'])
	{
		case 'upload' : 
				$manager->upload($glob);
				break;
		case 'create' : 
				$manager->create($glob);
				break;
		case 'del' : 
				$manager->delete($glob);
				break;		
	}
	
	switch ($glob['type']){
    	case 'image':
    		include_once('imgbrowser.php');
    		break;
    	case 'file':
    		include_once('filebrowser.php');
    		break;	
    }
    
?>