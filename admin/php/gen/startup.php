<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
error_reporting(0);
ini_set('date.timezone', 'GMT');
define('ADMIN_PATH',$script_path);
session_register(UID);

//foreach($HTTP_GET_VARS as $key => $value)
foreach($_GET as $key => $value)
    {
        $glob[$key]=$value;
    }


//foreach($HTTP_POST_VARS as $key => $value)
foreach($_POST as $key => $value)
    {
        $glob[$key]=$value;
    }

include_once(ADMIN_PATH."misc/cls_mysql_db.php");
include_once(ADMIN_PATH."config/config.php");
include_once(ADMIN_PATH."misc/cls_ft.php");
include_once(ADMIN_PATH."misc/gen_lib.php");
include_once(ADMIN_PATH."misc/cms_admin_lib.php");
//include_once(ADMIN_PATH."misc/attributes_lib.php");
include_once(ADMIN_PATH."misc/security_lib.php");
include_once(ADMIN_PATH."misc/stlib.php");


include_once(ADMIN_PATH.'misc/faq_admin_lib.php');

//Global Variables
$menu_link_array=array();
$bottom_includes='';

?>
