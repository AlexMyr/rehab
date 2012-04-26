<?php 
/*
$mtpl['dynamic_menu_start']='    <link rel="stylesheet" type="text/css" href="h_menu.css">
    <script type="text/javascript" src="js/ie5.js"></script>
    <script type="text/javascript" src="js/dropdownmenu.js"></script>
		  <ul id="'.$cms_menu.'" class="h_menu">';
$mtpl['dynamic_menu_end']='		
</ul>
';
$mtpl['main_button_start']='<li>';
$mtpl['main_button_end']='</li>';

$mtpl['main_button_template']='<a class="item1" href="[!BUTTON_LINK!]"[!TARGET!]><span>[!BUTTON_TEXT!]</span></a>';

$mtpl['sub_button_simple_template']='<a class="item2" href="[!BUTTON_LINK!]" [!TARGET!]><span>[!BUTTON_TEXT!]</span></a>';
$mtpl['sub_button_follow_template']='<a class="item2" href="[!BUTTON_LINK!]" [!TARGET!]><span>[!BUTTON_TEXT!]</span></a>';


$mtpl['submenu_start']='
<div class="section">';
$mtpl['submenu_end']='
</div>';

$bottom_includes.='
	    <script type="text/javascript">
		var h_menu = new DropDownMenuX(\''.$cms_menu.'\');
		h_menu.delay.show = 0;
		h_menu.delay.hide = 50;
		h_menu.position.levelX.left = 0;
		h_menu.init();
		</script>
';
*/

$mtpl['dynamic_menu_start']='<ul id="'.$cms_menu.'" class="h_menu">';
$mtpl['dynamic_menu_end']='</ul>';
$mtpl['main_button_start']='<li>';
$mtpl['main_button_end']='</li>';

$mtpl['main_button_template']='<a class="item1" href="[!BUTTON_LINK!]"[!TARGET!]><span>[!BUTTON_TEXT!]</span></a>';

?>