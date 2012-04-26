<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
error_reporting(0);
ini_set("session.gc_maxlifetime", intval( 1 * ( 24 * ( 60 * 60 ) ) ) );
ini_set("session.cookie_lifetime", 0);
ini_set('date.timezone', 'GMT');
define('ADMIN_PATH',$script_path);

foreach($_GET as $key => $value)
    {
        $glob[$key]=$value;
    }


foreach($_POST as $key => $value)
    {
        $glob[$key]=$value;
    }

include_once(ADMIN_PATH."misc/cls_mysql_db.php");
include_once(ADMIN_PATH."config/config.php");
include_once(ADMIN_PATH."misc/cls_dynamic_menu.php");
include_once(ADMIN_PATH."misc/cls_ft.php");
include_once(ADMIN_PATH."misc/gen_lib.php");
include_once(ADMIN_PATH."misc/cms_front_lib.php");
include_once(ADMIN_PATH."misc/security_lib.php");
include_once(ADMIN_PATH."php/gen/func_perm.php");
include_once(ADMIN_PATH."php/gen/page_perm.php");
include_once(ADMIN_PATH."misc/stlib.php");



//Global Variables
$menu_link_array=array();
$bottom_includes='';

?>
