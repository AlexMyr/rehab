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
'<div class="cornerTable"><table class="pricePlanTable">
        <tr>
            <th class="priceTableHead"></th>
';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<th class="priceTableHead">'.$price_plan_name[$i].'</th>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.LOGO'].'</td>';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<td>'.($has_logo[$i] ? '&#10004;' : '').'</td>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.CREATE_PROGRAM'].'</td>';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<td>'.($can_create_exercise[$i] ? '&#10004;' : '').'</td>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.EMAIL'].'</td>';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<td>'.($email[$i] ? '&#10004;' : '').'</td>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.PHOTO'].'</td>';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<td>'.($photo_lineart[$i] ? '&#10004;' : '').'</td>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.NUMBER_USERS'].'</td>';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<td>'.$user_count[$i].'</td>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.EXPIRY'].'</td>';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<td>'.$licence_period[$i].'</td>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.COST'].'</td>';
for($i=0;$i<$payment_count;$i++)
{
    $payment_table .= '<td>'.$price_value[$i].'</td>';
}
$payment_table .= '</tr>';

$payment_table .= '<tr>
            <td class="priceTableLeftColumn">'.$tags['T.CHOOSE'].'</td>';

for($i=0;$i<$payment_count;$i++)
{//$tags['T.CHOOSE']
    if($price_amount[$i] === '0'){
        $payment_table .= '<td><a id="submitPayment" class="moreBtn" style="margin-bottom: 5px; margin-left:10px; width:105px;" href="index.php?act=member-pay&pay_type=per_year&pag=profile_payment&price_id='.$price_plan_id[$i].'">
                            <span>'.$tags['T.CHOOSE'].'</span>
                        </a></td>';
        continue;
    }
    if(substr_count($price_value[$i],'POA')){
        $payment_table .= '<td><a class="moreBtn" id="contactus" style="margin-left:25px; margin-right: 25px; width:85px;" href="index.php?pag=cms&p=contact_us">
                                    <span>'.$tags['T.CONTACT'].'</span>
                                </a></td>';
        continue;
    }
    if(($current_price_id != $price_plan_id[$i] && $active == 2 && !$is_trial) OR ($is_trial && $price_amount[$i] === '0')){
        $payment_table .= '<td><span>'.$tags['T.ALREADY'].'</span></td>';
        continue;
    }
    $payment_table .= '<td>'./*
						'<a class="moreBtn" style="margin-bottom: 5px; margin-left:10px; width:105px;" href="index.php?act=member-pay&pay_type=per_year&pag=profile_payment&price_id='.$price_plan_id[$i].'">
                            <span>'.$tags['T.YEAR'].'</span>
                        </a>'.*/
                        '<a class="moreBtn" style="margin-bottom: 5px; margin-left:10px; width:105px;" href="index.php?act=member-pay&pay_type=yearly&pag=profile_payment&price_id='.$price_plan_id[$i].'">
                            <span>'.$tags['T.YEARLY'].'</span>
                        </a>
                        <a class="moreBtn" style="margin-left:10px; width:105px;" href="index.php?act=member-pay&pay_type=monthly&pag=profile_payment&price_id='.$price_plan_id[$i].'">
                            <span>'.$tags['T.MONTH'].'</span>
                        </a>
                    </td>';
}
$payment_table .= '</tr></table></div>';
$ft->assign('PAYMENT_TABLE', $payment_table);

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');

?>