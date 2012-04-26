<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
?>
<?php
/*
<table border=5 bordercolor=red title="Debug information" width=100% bgcolor="#FFFFFF">
<tr>
	<td colspan=2 valign="top">
		<b>Session Id:</b> <?php echo session_id() ?><br>
	 	<b>Language:</b><?echo $lang?>
	</td>
	<td colspan=2 valign="top">
		<b>Function: </b><?echo $glob['act']?> <br>
		<b>Page: </b><?echo $glob['pag']?><br>
		<b>Permission Level: </b><?echo $user_level?>
	</td>
</tr>
<tr>
	<td colspan=4><b>Current Directory: </b><?echo getcwd() ?></td>
</tr>

<tr>
	<td colspan=2><b>$glob[] collection</b></td>
	<td colspan=2><b>$_SESSION content</b></td>
</tr>
<tr>
	<td valign="top" colspan=2><pre><?php print_r($glob) ?></pre></td>
	<td valign="top" colspan=2><pre><?php print_r($_SESSION) ?></pre></td>
</tr>

</table>
*/
?>
<table border="5" bordercolor="red" title="Debug information" width=100% bgcolor="#FFFFFF" style="clear:both;" class="">
<tr>
	<td  valign="top">
		<b>Session Id:</b> <?php echo session_id() ?><br>
	 	<b>Language:</b><?echo $lang?>
	</td>
	<td  valign="top">
		<b>Function: </b><?echo $glob['act']?> <br>
		<b>Page: </b><?echo $glob['pag']?><br>
		<b>Permission Level: </b><?echo $user_level?>
	</td>
</tr>
<tr>
	<td colspan="2"><b>Current Directory: </b><?echo getcwd() ?></td>
</tr>

<tr>
	<td ><b>$glob[] collection</b></td>
	<td ><b>$_SESSION content</b></td>
</tr>
<tr>
	<td valign="top" ><pre><?php print_r($glob) ?></pre><pre><?php print_r($_FILES) ?></pre></td>
	<td valign="top" ><pre><?php print_r($_SESSION) ?></pre>&nbsp;</td>	
</tr>
<?php
if(function_exists('get_debug_instance')): ?>

<tr>
	<td colspan="2"><b>MySQL: </b></td>
</tr>
<tr><td colspan="2">
<?php
	$bug = & get_debug_instance();
	echo '<pre>';
	print_r($bug->display());
	echo '</pre>';
?>
</td></tr>
<?php endif; ?>
</table>