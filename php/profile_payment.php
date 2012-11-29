<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$dbu = new mysql_db();

$tags = get_template_tag($glob['pag'], $glob['lang']);
foreach($tags as $name => $row){
  $ft->assign($name, $row);
}

$dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");
$dbu->move_next();

if(isset($glob['paym'])){
	if($glob['paym']){
		$glob['error'] = 'Thank you for paying.';
		$glob['success'] = true;
	}
	else{
		$glob['error'] = 'Error. Please try again or contact administration.';
		$glob['success'] = false;
	}
}

$is_trial = $dbu->f('is_trial');
$active = $dbu->f('active');

/* request getting the current plan for current lang */
$dbu->query("SELECT price_id FROM `price_plan_new` WHERE currency = '".$currency."'
                AND price_plan_name = (SELECT price_plan_name FROM `trainer` INNER JOIN `price_plan_new` ON (price_id=price_plan_id)
                                            WHERE trainer_id='".$_SESSION[U_ID]."') ");
$dbu->move_next();
$current_price_id = $dbu->f('price_id');


$ft->define(array('main' => "profile_payment.html"));

$pag = isset($glob['pag']) ? $glob['pag'] : pathinfo(__FILE__, PATHINFO_FILENAME);
$tags = get_template_tag($glob['pag'], $glob['lang']);

$ft->assign('CSS_PAGE', $glob['pag']);
if($active==0)
    $page_title = isset($tags['T.EXPIRED']) ? $tags['T.EXPIRED'] : 'Your Trial Account Has Expired';
else 
    $page_title = isset($tags['T.CHOOSE']) ? $tags['T.CHOOSE'] : 'Choose a Payment Plan';
$ft->assign('PAGE_TITLE', $page_title);
$ft->define_dynamic('price_user_line','main');
$ft->define_dynamic('price_clinic_line','main');

$query = $dbu->query("select * from price_plan_new where is_active = 1 AND currency='".$currency."'");
$price_plan_id = array();
$price_plan_name = array();
$has_logo = array();
$can_create_exercise = array();
$email = array();
$photo_lineart = array();
$user_count = array();
$licence_period = array();
$price_value = array();
$price_amount = array();
$is_active = array();

$payment_count = 0;
$payment_plans = array();

while($query->next())
{
    $price_plan_id[] = $query->f('price_id');
    $price_plan_name[] = $query->f('price_plan_name');
    $has_logo[] = $query->f('has_logo');
    $can_create_exercise[] = $query->f('can_create_exercise');
    $email[] = $query->f('email');
    $photo_lineart[] = $query->f('photo_lineart');
    $user_count[] = $query->f('licence_amount');
    $licence_period[] = $query->f('licence_period');
    $price_value[] = $query->f('price_view');
    $price_amount[] = $query->f('price_value');
    
    $payment_count++;
}

//form payment table

$payment_table =
'<div class="cornerTable" style="border:none;">
  <div class="paymTableHeaderDiv" style="background: url(\'../img/paymTableHeaderDivBordered.png\') no-repeat scroll top center transparent; display: block; float: right; height: 30px; width: 640px;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	  $border_style = '';
	$payment_table .= '<span style="color:#FFFFFF; text-shadow: none; display: inline-block; text-align: center; padding-top:5px; height:26px; '.$border_style.' width:'.$column_width.'px;">'.$price_plan_name[$i].'</span>';
  }
  
$payment_table .= '</div>
  <div style="clear:both;"></div>
  <div style="background: url(\'../img/paymTableLeftDivBordered.png\') no-repeat scroll top center transparent; display: block; float: left; height: 305px; width: 160px; position: absolute;">
	<div style="color:#FFFFFF; text-shadow: none; height:28px; border-bottom: 1px solid #444444;"><div style="padding:5px;">'.$tags['T.LOGO'].'</div></div>
	<div style="color:#FFFFFF; text-shadow: none; height:42px; border-bottom: 1px solid #444444;"><div style="padding-left:5px;">'.$tags['T.CREATE_PROGRAM'].'</div></div>
	<div style="color:#FFFFFF; text-shadow: none; height:27px; border-bottom: 1px solid #444444;"><div style="padding:5px;">'.$tags['T.EMAIL'].'</div></div>
	<div style="color:#FFFFFF; text-shadow: none; height:27px; border-bottom: 1px solid #444444;"><div style="padding:5px;">'.$tags['T.PHOTO'].'</div></div>
	<div style="color:#FFFFFF; text-shadow: none; height:42px; border-bottom: 1px solid #444444;"><div style="padding-left:5px;">'.$tags['T.NUMBER_USERS'].'</div></div>
	<div style="color:#FFFFFF; text-shadow: none; height:28px; border-bottom: 1px solid #444444;"><div style="padding:5px;">'.$tags['T.EXPIRY'].'</div></div>
	<div style="color:#FFFFFF; text-shadow: none; height:28px; border-bottom: 1px solid #444444;"><div style="padding:5px;">'.$tags['T.COST'].'</div></div>
	<div style="color:#FFFFFF; text-shadow: none; height:75px;"><div style="padding:25px 5px;">'.$tags['T.CHOOSE'].'</div></div>
  </div>';
  
  $payment_table .= '<div class="pricePlanTable" style="float: right; border-right: 2px solid #444444; border-bottom: 2px solid #444444;height: 303px; width: 638px;">';
  
  $payment_table .= '<div style="height:28px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:28px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;">'.($has_logo[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px;"></span>' : '<span style="display: inline-block;height: 26px;width: 26px;"></span>').'</div>';
  }
  $payment_table .= '</div>';
  
  $payment_table .= '<div style="height:42px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:42px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;">'.($can_create_exercise[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px; padding-top:12px;"></span>' : '<span style="display: inline-block;height: 26px;width: 26px;"></span>').'</div>';
  }
  $payment_table .= '</div>';
  
  $payment_table .= '<div style="height:27px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:27px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;">'.($email[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px;"></span>' : '<span style="display: inline-block;height: 26px;width: 26px;"></span>').'</div>';
  }
  $payment_table .= '</div>';
  
  $payment_table .= '<div style="height:27px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:27px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;">'.($photo_lineart[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px;"></span>' : '<span style="display: inline-block;height: 26px;width: 26px;"></span>').'</div>';
  }
  $payment_table .= '</div>';
  
  $payment_table .= '<div style="height:42px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:42px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;"><span style="font-size:16px; display: block; padding-top:10px;font-weight: normal;">'.$user_count[$i].'</span></div>';
  }
  $payment_table .= '</div>';
  
  $payment_table .= '<div style="height:28px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:28px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;"><span style="font-size:16px; display: block; font-weight: normal;">'.$licence_period[$i].'</span></div>';
  }
  $payment_table .= '</div>';
  
  $payment_table .= '<div style="height:28px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:28px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;"><span style="font-size:16px; display: block; font-weight: normal;">'.$price_value[$i].'</span></div>';
  }
  $payment_table .= '</div>';
  
  $payment_table .= '<div style="height:75px; border-bottom: 1px solid #444444;">';
  for($i=0;$i<$payment_count;$i++)
  {
	$column_width = 159;
	$border_style = 'border-right:1px solid #444444;';
	if($i>2)
	{
	  $border_style = '';
	  $column_width = 158;
	}
	

	if($price_amount[$i] === '0'){
	  $payment_table .= '<div class="priceTableHead" style="float: left; height:75px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;"><a id="submitPayment" class="moreBtn" style="width:105px; background:none;" href="index.php?act=member-pay&pay_type=per_year&pag=profile_payment&price_id='.$price_plan_id[$i].'">
						  <span style="font-weight: normal;margin-top:20px; padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;" class="curvyCorner">'.$tags['T.CHOOSE'].'</span>
					  </a></div>';
	  continue;
	}
	if(substr_count($price_value[$i],'POA')){
	  $payment_table .= '<div class="priceTableHead" style="float: left; height:75px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;">
						  <a id="submitPayment" class="moreBtn" style="width:105px; background:none;" href="index.php?act=member-pay&pay_type=per_year&pag=profile_payment&price_id='.$price_plan_id[$i].'">
							<span style="font-weight: normal;margin-top:20px; padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;" class="curvyCorner">'.$tags['T.CONTACT'].'</span>
						  </a>
						</div>';
	  continue;
	}
	if(($current_price_id != $price_plan_id[$i] && $active == 2 && !$is_trial) OR ($is_trial && $price_amount[$i] === '0')){
	  $payment_table .= '<div class="priceTableHead" style="float: left; height:75px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;">
							  <span style="font-size:16px; display: block; font-weight: normal; margin-top:15px;">'.$tags['T.ALREADY'].'</span>
						  </div>';
	  continue;
	}
	$payment_table .= '<div class="priceTableHead" style="float: left; height:75px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;">
						  <a class="moreBtn" style="background:none; width:105px; margin:5px 0px;" href="index.php?act=member-pay&pay_type=yearly&pag=profile_payment&price_id='.$price_plan_id[$i].'">
						  <span style="font-weight: normal;padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;" class="curvyCorner">'.$tags['T.YEARLY'].'</span>
						</a>
						<a class="moreBtn" style="background:none;width:105px;" href="index.php?act=member-pay&pay_type=monthly&pag=profile_payment&price_id='.$price_plan_id[$i].'">
						  <span style="font-weight: normal;padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;" class="curvyCorner">'.$tags['T.MONTH'].'</span>
						</a>
						</a></div>';
	$payment_table .= '<div class="priceTableHead" style="float: left; height:75px; text-align: center; padding:0px; '.$border_style.' width:'.$column_width.'px;"><span style="font-size:16px; display: block; font-weight: normal;">'.$price_value[$i].'</span></div>';
  }
  $payment_table .= '</div>';
  
  
  $payment_table .= '</div>';
//$payment_table .= '<table class="pricePlanTable" style="border-right: 2px solid #444444; border-bottom: 2px solid #444444;height: 305px; width: 639px;">';
//
//$payment_table .= '<tr style="height:28px;">';
//for($i=0;$i<$payment_count;$i++)
//{
//    $payment_table .= '<td class="priceTableHead" style="padding:0px;'.($i==0 ? 'border-left:none;' : '').'">'.($has_logo[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px;"></span>' : '').'</td>';
//}
//$payment_table .= '</tr>';
//
//$payment_table .= '<tr style="height:43px;">';
//for($i=0;$i<$payment_count;$i++)
//{
//    $payment_table .= '<td style="padding:0px;'.($i==0 ? 'border-left:none;' : '').'">'.($can_create_exercise[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px;"></span>' : '').'</td>';
//}
//$payment_table .= '</tr>';
//
//$payment_table .= '<tr style="height:28px;">';
//for($i=0;$i<$payment_count;$i++)
//{
//  $payment_table .= '<td style="padding:0px;'.($i==0 ? 'border-left:none;' : '').'">'.($email[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px;"></span>' : '').'</td>';
//}
//$payment_table .= '</tr>';
//
//$payment_table .= '<tr style="height:28px;">';
//for($i=0;$i<$payment_count;$i++)
//{
//    $payment_table .= '<td style="padding:0px;'.($i==0 ? 'border-left:none;' : '').'">'.($photo_lineart[$i] ? '<span style="background: url(\'../img/tick_green_middle.png\') no-repeat scroll right center transparent;display: inline-block;height: 26px;width: 26px;"></span>' : '').'</td>';
//}
//$payment_table .= '</tr>';
//
//$payment_table .= '<tr style="height:43px;">';
//for($i=0;$i<$payment_count;$i++)
//{
//    $payment_table .= '<td style="'.($i==0 ? 'border-left:none;' : '').'">'.$user_count[$i].'</td>';
//}
//$payment_table .= '</tr>';
//
//$payment_table .= '<tr style="height:28px;">';
//for($i=0;$i<$payment_count;$i++)
//{
//    $payment_table .= '<td style="'.($i==0 ? 'border-left:none;' : '').'">'.$licence_period[$i].'</td>';
//}
//$payment_table .= '</tr>';
//
//$payment_table .= '<tr style="height:28px;">';
//for($i=0;$i<$payment_count;$i++)
//{
//    $payment_table .= '<td style="'.($i==0 ? 'border-left:none;' : '').'">'.$price_value[$i].'</td>';
//}
//$payment_table .= '</tr>';
//
//$payment_table .= '<tr>';
//
//for($i=0;$i<$payment_count;$i++)
//{//$tags['T.CHOOSE']
//    if($price_amount[$i] === '0'){
//        $payment_table .= '<td style="'.($i==0 ? 'border-left:none;' : '').'"><a id="submitPayment" class="moreBtn" style="width:105px; background:none;" href="index.php?act=member-pay&pay_type=per_year&pag=profile_payment&price_id='.$price_plan_id[$i].'">
//                            <span style="padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;" class="curvyCorner">'.$tags['T.CHOOSE'].'</span>
//                        </a></td>';
//        continue;
//    }
//    if(substr_count($price_value[$i],'POA')){
//        $payment_table .= '<td style="'.($i==0 ? 'border-left:none;' : '').'"><a class="moreBtn" id="contactus" style="width:105px; background:none;" href="index.php?pag=cms&p=contact_us">
//                                    <span class="curvyCorner" style="padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;">'.$tags['T.CONTACT'].'</span>
//                                </a></td>';
//        continue;
//    }
//    if(($current_price_id != $price_plan_id[$i] && $active == 2 && !$is_trial) OR ($is_trial && $price_amount[$i] === '0')){
//        $payment_table .= '<td><span>'.$tags['T.ALREADY'].'</span></td>';
//        continue;
//    }
//    $payment_table .= '<td style="'.($i==0 ? 'border-left:none;' : '').'">
//                        <a class="moreBtn" style="background:none; width:105px; margin-bottom:5px;" href="index.php?act=member-pay&pay_type=yearly&pag=profile_payment&price_id='.$price_plan_id[$i].'">
//                            <span style="padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;" class="curvyCorner">'.$tags['T.YEARLY'].'</span>
//                        </a>
//                        <a class="moreBtn" style="background:none;width:105px;" href="index.php?act=member-pay&pay_type=monthly&pag=profile_payment&price_id='.$price_plan_id[$i].'">
//                            <span style="padding-left:10px;height:31px; background: url(\'../img/green_btn.png\') no-repeat scroll center top transparent;text-align:center;" class="curvyCorner">'.$tags['T.MONTH'].'</span>
//                        </a>
//                    </td>';
//}
//$payment_table .= '</tr></table></div>';
$payment_table .= '</div>';
$ft->assign('PAYMENT_TABLE', $payment_table);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');

?>