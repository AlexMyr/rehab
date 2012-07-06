<?php

/* PAP4 integration */
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, "http://rehabmypatient.com/affiliate/plugins/PayPal/paypal.php");
 curl_setopt($ch, CURLOPT_POST, 1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
 curl_exec($ch);
/* end of PAP4 integration */
logPayment('', $_GET, 'IPN_message_GET');
logPayment('', $_POST, 'IPN_message');
mail('cardioprint@miralex.com.ua', 'IPN message', 'PayPal IPN message is received at Rehabmypatient. Log is at paypal_transactions table', 'From: paypal@rehabmypatient.com' );

function logPayment($request, $answer, $type){
    include('config/config.php');
    $con = mysql_connect(MYSQL_DB_HOST,MYSQL_DB_USER,MYSQL_DB_PASS) ;
    mysql_select_db(MYSQL_DB_NAME,$con);
    
    parse_str($request);
    
    if(!isset($AMT)) $AMT = $answer['AMT']; if(!isset($AMT)) $AMT = $PAYMENTREQUEST_0_AMT;
    if(!isset($CURRENCYCODE)) $CURRENCYCODE = $answer['CURRENCYCODE'];
    $STATUS = isset($answer['PROFILESTATUS']) ? $answer['PROFILESTATUS'] : $answer['STATUS'];
    $ERROR = $answer['L_SEVERITYCODE0'] == 'Error' ? implode(' | ', array($answer['L_ERRORCODE0'],$answer['L_SHORTMESSAGE0'], $answer['L_LONGMESSAGE0'])) : NULL;
    $PROFILE_ID = isset($answer['PROFILEID']) ? $answer['PROFILEID'] : $answer['recurring_payment_id'];
    
    $trainer_id = $_SESSION['m_id'];
    if(!$trainer_id){
        $q = mysql_fetch_array(mysql_query("SELECT trainer_id FROM trainer WHERE paypal_profile_id='".$PROFILE_ID."'"));
        $trainer_id = $q[0];
    }
    foreach($answer as $key=>$val)
        $answer_str .= '&'.$key.'='.$val;
    
    mysql_query("INSERT INTO `paypal_transactions`
                            (`trainer_id`, `name`, `profile_id`, `status`, `type`, `amount`, `currency`, `timestamp`, `ack`, `request`, `correlation_id`, `error`, `answer`)
                     VALUES ('$trainer_id', '$DESC', '$PROFILE_ID', '$STATUS', '$type', '$AMT', '$CURRENCYCODE', '{$answer['TIMESTAMP']}', '{$answer['ACK']}', '$request', '{$answer['CORRELATIONID']}',
                            '$ERROR', '$answer_str')");
}
?>