<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");

$dbu = new mysql_db();

$dbu->query("select * from trainer where trainer_id=".$_SESSION[U_ID]." ");

$dbu->move_next();

if(isset($glob['paym']))
{
	if($glob['paym'])
	{
		$glob['error'] = 'Thank you for paying.';
		$glob['success'] = true;
	}
	else
	{
		$glob['error'] = 'Error. Please try again or contact administration.';
		$glob['success'] = false;
	}
}

if($dbu->f('active')!=0 && $dbu->f('is_trial')!=1) 
{
	
	$ft->define(array('main' => "profile_payment_done.html"));
	$page_title = "Payment Plan";
	$ft->assign('PAGE_TITLE', $page_title);
}
else
	{
		$ft->define(array('main' => "profile_payment.html"));
		$ft->assign('CSS_PAGE', $glob['pag']);
		if($dbu->f('active')==0) $page_title = "Your Trial Account Has Expired";
		else $page_title = "Choose a Payment Plan";
		$ft->assign('PAGE_TITLE', $page_title);
		$ft->define_dynamic('price_user_line','main');
		$ft->define_dynamic('price_clinic_line','main');
		
		$query = $dbu->query("select * from price_plan_new where is_active = 1 ");
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
		'<table class="pricePlanTable">
				<tr>
					<th class="priceTableHead"></th>
		';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<th class="priceTableHead">'.$price_plan_name[$i].'</th>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Place logo</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<td>'.($has_logo[$i] ? '&#10004;' : '').'</td>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Create an Exercise Programme</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<td>'.($can_create_exercise[$i] ? '&#10004;' : '').'</td>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Email</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<td>'.($email[$i] ? '&#10004;' : '').'</td>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Photo and lineart</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<td>'.($photo_lineart[$i] ? '&#10004;' : '').'</td>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Number of users at any one time</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<td>'.$user_count[$i].'</td>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Expiry</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<td>'.$licence_period[$i].'</td>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Cost</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			$payment_table .= '<td>'.$price_value[$i].'</td>';
		}
		$payment_table .= '</tr>';
		
		$payment_table .= '<tr>
					<td class="priceTableLeftColumn">Choose plan</td>';
		for($i=0;$i<$payment_count;$i++)
		{
			if(substr_count($price_value[$i],'POA'))
				$payment_table .= '<td>Contact us</td>';
			else
			{
				$payment_table .= '<td>
										<a class="moreBtn" id="submitPayment" style="margin-left:25px; width:85px;" href="index.php?act=member-pay&pag=profile_do_payment&price_id='.$price_plan_id[$i].'">
											<span>Choose plan</span>
										</a>
										
									</td>';
			}
		}
		$payment_table .= '</tr></table>';
		//<input class="changePaymentRadio" type="radio" name="price_plan" value="'.$price_plan_id[$i].'" />
		$ft->assign('PAYMENT_TABLE', $payment_table);
		
		//$price = $dbu->query("select price_plan.* from price_plan where 1=1 AND licence_period='month' ");
		//
		//$i=0;
		//
		//while ($price->next())
		//	{
		//
		//		$dbu->query("select price_plan.* from price_plan where 1=1 AND licence_amount='".$price->f('licence_amount')."' AND licence_period='year' AND licence_type='".$price->f('licence_type')."' ");
		//		$dbu->move_next();
		//
		//		/* convert currency to html entity to show on page */
		//		if($price->f('currency')=="GBP") $currency = "&pound;";
		//		else if($price->f('currency')=="USD") $currency = "&#36;";
		//		else if($price->f('currency')=="EUR") $currency = "&euro;";
		//		else if($dbu->f('currency')=="GBP") $currency = "&pound;";
		//		else if($dbu->f('currency')=="USD") $currency = "&#36;";
		//		else if($dbu->f('currency')=="EUR") $currency = "&euro;";
		//		if(!is_numeric($price->f('price_value')) || !is_numeric($dbu->f('price_value')))
		//			{
		//				$currency = "";
		//				$m_price_link = "";
		//				$y_price_link = "";
		//			}
		//		else
		//			{
		//				$m_price_link = '<a href="index.php?pag=profile_do_payment&price_id='.$price->f('price_id').'" class="moreBtn"><span>Buy Now</span></a>';
		//				$y_price_link = '<a href="index.php?pag=profile_do_payment&price_id='.$dbu->f('price_id').'" class="moreBtn"><span>Buy Now</span></a>';
		//			}
		//		$ft->assign(array(
		//			'LICENCE_AMOUNT'=>$price->f('licence_amount'),
		//			'M_PRICE_LINK'=>$m_price_link,
		//			'M_PRICE_CURRENCY'=>$currency,
		//			'M_PRICE_VALUE'=>$price->f('price_value'),
		//			'Y_PRICE_LINK'=>$y_price_link,
		//			'Y_PRICE_CURRENCY'=>$currency,
		//			'Y_PRICE_VALUE'=>$dbu->f('price_value'),
		//		));
		//		if($price->f('licence_type')=="user") $ft->parse('PRICE_USER_LINE_OUT','.price_user_line');
		//		else if($price->f('licence_type')=="clinic") $ft->parse('PRICE_CLINIC_LINE_OUT','.price_clinic_line');
		//		$i++;
		//	}
	}

$site_meta_title=$meta_title." - Payment";
$site_meta_keywords=$meta_keywords.", Payment";
$site_meta_description=$meta_description." Payment";

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');

return $ft->fetch('CONTENT');

?>