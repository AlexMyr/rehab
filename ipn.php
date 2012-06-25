<?php
logPayment('', $_POST, 'IPN_message_POST');
logPayment('', $_GET, 'IPN_message_GET');
mail('cardioprint@miralex.com.ua', 'IPN message', 'PayPal IPN message received at Rehabmypatient. Log is at paypal_transactions table', 'From: paypal@rehabmypatient.com' );

function logPayment($request, $answer, $type){
    include('config/config.php');
    $con = mysql_connect(MYSQL_DB_HOST,MYSQL_DB_USER,MYSQL_DB_PASS) ;
    mysql_select_db(MYSQL_DB_NAME,$con);
    
    parse_str($request);
    
    if(!isset($AMT)) $AMT = $answer['AMT']; if(!isset($AMT)) $AMT = $PAYMENTREQUEST_0_AMT;
    if(!isset($CURRENCYCODE)) $CURRENCYCODE = $answer['CURRENCYCODE'];
    $STATUS = isset($answer['PROFILESTATUS']) ? $answer['PROFILESTATUS'] : $answer['STATUS'];
    $ERROR = $answer['L_SEVERITYCODE0'] == 'Error' ? implode(' | ', array($answer['L_ERRORCODE0'],$answer['L_SHORTMESSAGE0'], $answer['L_LONGMESSAGE0'])) : NULL;
    
    $trainer_id = $_SESSION['m_id'];
    foreach($answer as $key=>$val)
        $naswer_str .= '&'.$key.'='.$val;
    
    mysql_query("INSERT INTO `paypal_transactions`
                            (`trainer_id`, `name`, `profile_id`, `status`, `type`, `amount`, `currency`, `timestamp`, `ack`, `request`, `correlation_id`, `error`, `answer`)
                     VALUES ('$trainer_id', '$DESC', '{$answer['PROFILEID']}', '$STATUS', '$type', '$AMT', '$CURRENCYCODE', '{$answer['TIMESTAMP']}', '{$answer['ACK']}', '$request', '{$answer['CORRELATIONID']}',
                            '$ERROR', '$naswer_str')");
}
?>