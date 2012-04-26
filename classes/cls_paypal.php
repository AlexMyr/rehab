<?php
/**
 * Paypal base, which has the basic common info for both implementations
 */
class paypal_base{
	function paypal_base(){
		session_register(UID);
	}
	/**
	 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
	 * It is usefull to search for a particular key and displaying arrays.
	 *
	 * @param string $nvpstr
	 * @return array
	 */
	function deformatNVP($nvpstr){
		$intial=0;
		$nvpArray = array();
		
		while(strlen($nvpstr)){
			//postion of Key
			$keypos = strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
			
			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval = substr($nvpstr,$intial,$keypos);
			$valval = substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] = urldecode( $valval);
			$nvpstr = substr($nvpstr,$valuepos+1,strlen($nvpstr));
		}
		return $nvpArray;
	}	
}

/**
 * PayPal US class
 *
 */
class paypal_us extends paypal_base {
	
	/**
	 * Call this method to perfom an SetExpressCheckout call on the PayPal API
	 * This is the US version
	 *
	 * @param array $param(site_url,amount,currencyCode,returnURLParams,cancelURL)
	 * @return bool 
	 */
	function expressCheckout(&$param){
		unset($_SESSION['reshash']);
		unset($_SESSION['nvpReqArray']);		
		if(!is_array($param)){
			$param['error'] = 'No param data sent!.';
			return false;
		}
		$url = $param['site_url'];	
		$paymentAmount = $param['amount'];
		$currencyCodeType = $param['currencyCode'];
		$paymentType = 'Sale';
		$returnURL = urlencode($url.($param['returnURLParams'] ? 'pp.php?amount='.$param['amount'].'&'.$param['returnURLParams'] :'pp.php?amount='.$param['amount']));
		$cancelURL = urlencode($param['cancelURL']);
		// create the npstring	   	
		$nvpstr="&Amt=".$paymentAmount."&PAYMENTACTION=".$paymentType."&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType;
		//make the call
		$resArray = $this->_hash_call("SetExpressCheckout",$nvpstr);
		$ack = strtoupper($resArray["ACK"]);
		//if success then redirect to the login screen
		// else display the errors
		if($ack=="SUCCESS"){
			$token = urldecode($resArray["TOKEN"]);
			$payPalURL = PAYPAL_URL.'?cmd=_express-checkout&token='.$token;
			header("Location: ".$payPalURL);
		}else{					 
		    $count=0;
			while (isset($resArray["L_SHORTMESSAGE".$count]))
			{
		  		$param['error'] .= $resArray["L_LONGMESSAGE".$count].'<br />'; 
		  		$count++;
			}
			return false;
		}
		return true;
	}
	
	/**
	 * Call this function to check a payment has been made with paypal Express Checkout
	 *
	 * @param unknown_type $ld
	 * @return unknown
	 */
	function expressPayment(&$ld){
		//this function is called when loginpaypal returns successfully
		//we send some vars with the url and check that everything is okey and has not been altered
		//we get the details
		$token = urlencode( $ld['token']);
		$nvpstr="&TOKEN=".$token;
		//include de api
		//create the nvpstring for actualy removing the money from the client and adding it to the seller
		$paymentAmount =urlencode ($ld['amount']);
		$paymentType = urlencode("Sale");
		$currCodeType = $ld['currency'];
		$payerID = urlencode($ld['PayerID']);
		$serverName = urlencode($_SERVER['SERVER_NAME']);
		
		$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;
		//make the call
		$resArray=$this->_hash_call("DoExpressCheckoutPayment",$nvpstr);
		$ack = strtoupper($resArray["ACK"]);
		if($ack!="SUCCESS")
		{
		  	    $count=0;
				while (isset($resArray["L_SHORTMESSAGE".$count]))
				{
			  		$ld['error'] .= $resArray["L_LONGMESSAGE".$count].'<br />'; 
			  		$count++;
				}
				return false;
		}
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
		return true;
	}
	
	/**
	 * Call this function to pay with a credit card using DirectPayment US
	 *
	 * @param array $param
	 * @return bool
	 */
	function directPayment(&$param){
		/**
		* Get required parameters from the web form for the request
		*/
		$firstName = urlencode( $param['first_name']);
		$lastName = urlencode( $param['last_name']);
		$creditCardType =urlencode( $param['creditCardType']);
		$creditCardNumber = urlencode($param['creditCardNumber']);
		$expDateMonth = urlencode( $param['expDateMonth']);
		
		// Month must be padded with leading zero
		$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
		
		$expDateYear = urlencode( $param['expDateYear']);
		$cvv2Number = urlencode($param['cvv2Number']);
		$address1 = urlencode($param['address1']);
		$address2 = urlencode($param['address2']);
		$city = urlencode($param['city']);
		$state = urlencode( $param['state']);
		$zip = urlencode($param['zip']);
		$amount = urlencode($param['amount']);
		$currencyCode = $param['currencyCode'];
		$country = urlencode($param['country']);
		$paymentType = urlencode("Sale");
		
		/* Construct the request string that will be sent to PayPal.
		The variable $nvpstr contains all the variables and is a
		name value pair string with & as a delimiter */
		$nvpstr="&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber&EXPDATE=".$padDateMonth.$expDateYear.
		"&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName&CURRENCYCODE=$currencyCode";
		
		/* Make the API call to PayPal, using API signature.
		The API response is stored in an associative array called $resArray */
		$resArray=$this->_hash_call("doDirectPayment",$nvpstr);
		
		/* Display the API response back to the browser.
		If the response from PayPal was a success, display the response parameters'
		If the response was an error, display the errors received using APIError.php.
		*/
		$ack = strtoupper($resArray["ACK"]);
		
		if($ack!="SUCCESS"){
			$count=0;
			while (isset($resArray["L_SHORTMESSAGE".$count]))
			{
				$ld['error'] .= $resArray["L_LONGMESSAGE".$count].'<br />'; 
				$count++;
			}
			return false;
		}
		unset($_SESSION['reshash']);
		unset($_SESSION['nvpReqArray']);		
		return true;		
	}

	/**
	 * Call this function to create a recurring payment
	 *
	 * @param array $param
	 * @return bool
	 */	
	function recurringPayment(&$param){
		
	}
	
	/**
	 * Function to perform the API call to PayPal using API signature
	 *
	 * @param string $methodName is name of API  method
	 * @param string $nvpStr
	 * @return array
	 */
	function _hash_call($methodName,$nvpStr){
		$API_UserName = API_USERNAME;
		$API_Password = API_PASSWORD;
		$API_Signature = API_SIGNATURE;
		$API_Endpoint = API_ENDPOINT;
		$version=VERSION;
	
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		//Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
		if(USE_PROXY){
			curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);	
			curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT); 
		}
		//NVPRequest for submitting to server
		$nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature).$nvpStr;
		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
	
		//getting response from server
		$response = curl_exec($ch);
		
		//convrting NVPResponse to an Associative Array
		$nvpResArray = $this->deformatNVP($response);
		$nvpReqArray = $this->deformatNVP($nvpreq);
		$_SESSION['nvpReqArray']=$nvpReqArray;
	
		if(curl_errno($ch)){
			//moving to display page to display curl errors
			$_SESSION['curl_error_no'] = curl_errno($ch) ;
			$_SESSION['curl_error_msg'] = curl_error($ch);
			$location = "perror.php";
			header("Location: $location");
		}else{
			//closing the curl
			curl_close($ch);
		}
		return $nvpResArray;
	}
}
//------------------
class paypal_uk extends paypal_base {
	
	/**
	 * Call this method to perfom an SetExpressCheckout call on the PayPal API
	 * This is the US version
	 *
	 * @param array $param(site_url,amount,currencyCode,returnURLParams,cancelURL)
	 * @return bool 
	 */
	function expressCheckout(&$param){
		unset($_SESSION['reshash']);
		unset($_SESSION['nvpReqArray']);		
		if(!is_array($param)){
			$param['error'] = 'No param data sent!.';
			return false;
		}
		$url = $param['site_url'];	
		$paymentAmount = $param['amount'];
		$currencyCodeType = $param['currencyCode'];
		$paymentType = 'Sale';
		$returnURL = $url.($param['returnURLParams'] ? 'pp.php?amount='.$param['amount'].'-'.$param['member'].'&'.$param['returnURLParams'] :'pp.php?amount='.$param['amount']);
		$cancelURL = $param['cancelURL'];
		// create the npstring	  
		$nvpstr ="&TRXTYPE=S&AMT=".$paymentAmount."&CANCELURL=".$cancelURL ."&RETURNURL=".$returnURL."&CURRENCY=".$currencyCodeType;
		//make the call
		$resArray = $this->_hash_call("SetExpressCheckout",$nvpstr);
		if($resArray['RESPMSG']=='Approved'){
			$token = urldecode($resArray["TOKEN"]);
			$payPalURL = PAYPAL_URL.'?cmd=_express-checkout&token='.$token;
			header("Location: ".$payPalURL);
		}else{					 
	  		$param['error'] .= $resArray["RESPMSG"].'<br />'; 
			return false;
		}
		return true;
	}
	
	/**
	 * Call this function to check a payment has been made with paypal Express Checkout
	 *
	 * @param array $ld
	 * @return unknown
	 */
	function expressPayment(&$ld){
		//this function is called when loginpaypal returns successfully
		//we send some vars with the url and check that everything is okey and has not been altered
		//we get the details
		$token = urlencode( $ld['token']);
		$nvpstr="&TOKEN=".$token;
		//include de api
		//create the nvpstring for actualy removing the money from the client and adding it to the seller
		$paymentAmount =urlencode ($ld['amount']);
		$paymentType = urlencode("Sale");
		$currCodeType = $ld['currency'];
		$payerID = urlencode($ld['PayerID']);
		$serverName = urlencode($_SERVER['SERVER_NAME']);
		
		$nvpstr ="&TOKEN=".$token."&PAYERID=".$payerID."&TRXTYPE=S&AMT=".$paymentAmount."&CANCELURL=".$cancelURL ."&RETURNURL=".$returnURL."&CURRENCY=".$currencyCodeType."&IPADDRESS=".$serverName;
		//make the call
		$resArray=$this->_hash_call("DoExpressCheckoutPayment",$nvpstr);
		if($resArray['RESPMSG']!='Approved'){
	  		$ld['error'] .= $resArray["L_LONGMESSAGE".$count].'<br />'; 
			return false;
		}
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
		return true;
	}
	
	/**
	 * Call this function to pay with a credit card using DirectPayment UK
	 *
	 * @param array $param
	 * @return bool
	 */
	function directPayment(&$param){
		/**
		* Get required parameters from the web form for the request
		*/
		$firstName = urlencode( $param['first_name']);
		$lastName = urlencode( $param['last_name']);
		$creditCardType =urlencode( $param['creditCardType']);
		$creditCardNumber = urlencode($param['creditCardNumber']);
		$expDateMonth = urlencode( $param['expDateMonth']);
		
		// Month must be padded with leading zero
		$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
		
		$expDateYear = urlencode( substr($param['expDateYear'],2,2));
		$cvv2Number = urlencode($param['cvv2Number']);
		$address1 = urlencode($param['address1']);
		$address2 = urlencode($param['address2']);
		$city = urlencode($param['city']);
		$state = urlencode( $param['state']);
		$zip = urlencode(str_replace(' ','',$param['zip']));
		$amount = urlencode($param['amount']);
		$currencyCode = $param['currencyCode'];
		$paymentType = urlencode("Sale");
		$country = urlencode($param['country']);

		$nvpQuery = array(
        'ACCT'       => $creditCardNumber,
        'CVV2'       => $cvv2Number,
        'EXPDATE'    => $padDateMonth.$expDateYear,
        'AMT'        => $amount,
        'CURRENCY'   => $currencyCode,
        'FIRSTNAME'  => $firstName,
        'LASTNAME'   => $lastName,
        'STREET'	 => $address1.'+'.$address2,
		'CITY'		 => $city,
		'STATE'		 => $state, 
		'ZIP' 		 => $zip,
		'COUNTRY'    => $country,
		'VERBOSITY'  => 'MEDIUM'
		);
       	$nvpstr = '';
       	foreach ($nvpQuery as $field => $val){
       		$nvpstr .=$field.'='.$val.'&';
       	}
		$nvpstr = trim($nvpstr,'&');
		/* Make the API call to PayPal, using API signature.
		The API response is stored in an associative array called $resArray */
		$resArray=$this->_hash_call("doDirectPayment",'&'.$nvpstr,'C');
		/* Display the API response back to the browser.
		If the response from PayPal was a success, display the response parameters'
		If the response was an error, display the errors received using APIError.php.
		*/
		
		if($resArray['RESPMSG']!='Approved'){
			$param['error'] .= $resArray['RESPMSG'].'<br />'; 
			return false;
		}
		unset($_SESSION['reshash']);
		unset($_SESSION['nvpReqArray']);		
		return true;		
	}
	
	/**
	 * Call this function to create a recurring payment
	 *
	 * @param array $param
	 * @return bool
	 */	
	function recurringPayment(&$param){
		/**
		* Get required parameters from the web form for the request
		*/
		$firstName = urlencode( $param['first_name']);
		$lastName = urlencode( $param['last_name']);
		$creditCardType =urlencode( $param['creditCardType']);
		$creditCardNumber = urlencode($param['creditCardNumber']);
		$expDateMonth = urlencode( $param['expDateMonth']);
		
		// Month must be padded with leading zero
		$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
		
		$expDateYear = urlencode( substr($param['expDateYear'],2,2));
		$cvv2Number = urlencode($param['cvv2Number']);
		$address1 = urlencode($param['address1']);
		$address2 = urlencode($param['address2']);
		$city = urlencode($param['city']);
		$state = urlencode( $param['state']);
		$zip = urlencode(str_replace(' ','',$param['zip']));
		$amount = urlencode($param['amount']);
		$currencyCode = $param['currencyCode'];
		$paymentType = urlencode("Sale");
		$country = urlencode($param['country']);
		$period = urlencode($param['period']);
		$term = urlencode($param['term']);

		
		$nvpQuery = array(
		'ACTION'       =>'A',
		'PROFILENAME' =>'BrandControllerAccount',
		'DESC' 		  =>'BrandControllerAccount',
		'START'       => date('dmY'),
		'PAYPERIOD'	  => $period,
		'TERM'		  => $term,
        'ACCT'       => $creditCardNumber,
        'CVV2'       => $cvv2Number,
        'EXPDATE'    => $padDateMonth.$expDateYear,
        'AMT'        => $amount,
        'CURRENCY'   => $currencyCode,
        'FIRSTNAME'  => $firstName,
        'LASTNAME'   => $lastName,
        'STREET'	 => $address1.'+'.$address2,
		'CITY'		 => $city,
		'STATE'		 => $state, 
		'ZIP' 		 => $zip,
		'COUNTRY'    => $country,
		'VERBOSITY'  => 'MEDIUM'
		);
       	$nvpstr = '';
       	foreach ($nvpQuery as $field => $val){
       		$nvpstr .=$field.'='.$val.'&';
       	}
		$nvpstr = trim($nvpstr,'&');
		/* Make the API call to PayPal, using API signature.
		The API response is stored in an associative array called $resArray */
		$resArray=$this->_hash_call("recurringPayment",'&'.$nvpstr,'C');
		/* Display the API response back to the browser.
		If the response from PayPal was a success, display the response parameters'
		If the response was an error, display the errors received using APIError.php.
		*/
		
		if($resArray['RESPMSG']!='Approved'){
			$param['error'] .= $resArray['RESPMSG'].'<br />'; 
			return false;
		}
		$param['resp'] = $resArray;
		unset($_SESSION['reshash']);
		unset($_SESSION['nvpReqArray']);		
		return true;		
	}
	
	function cancelRecurring(&$param){
		/**
		 * "TRXTYPE=R&TENDER=C&PARTNER=PayPal&VENDOR=Acme&USER=Acme&PWD=a1b2c3d4&ACTION=C&ORIGPROFILEID=RP000000001234"
		 */
		$nvpQuery = array(
		'ACTION'       =>'C',
		'ORIGPROFILEID' => $param['profileid']
		);
       	$nvpstr = '';
       	foreach ($nvpQuery as $field => $val){
       		$nvpstr .=$field.'='.$val.'&';
       	}
		$nvpstr = trim($nvpstr,'&');
		/* Make the API call to PayPal, using API signature.
		The API response is stored in an associative array called $resArray */
		$resArray=$this->_hash_call("recurringPayment",'&'.$nvpstr,'C');
		/* Display the API response back to the browser.
		If the response from PayPal was a success, display the response parameters'
		If the response was an error, display the errors received using APIError.php.
		*/
		
		if($resArray['RESPMSG']!='Approved'){
			$param['error'] .= $resArray['RESPMSG'].'<br />'; 
			return false;
		}
		unset($_SESSION['reshash']);
		unset($_SESSION['nvpReqArray']);		
		return true;		
	}
	
	
	/**
	 * Function to perform the API call to PayPal using API signature
	 *
	 * @param string $methodName is name of API  method
	 * @param string $nvpStr
	 * @return array
	 */
	function _hash_call($methodName,$nvpStr,$tender='P'){
		$API_UserName = API_USERNAME;
		$API_Password = API_PASSWORD;
		$API_Signature = API_SIGNATURE;
		$API_Endpoint = API_ENDPOINT;
		$version=VERSION;
	
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		//Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
		if(USE_PROXY){
			curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);	
			curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT); 
		}
		//NVPRequest for submitting to server
		switch ($methodName){
			case 'SetExpressCheckout':
				$methodName = 'S';
				$nvpreq="ACTION=".$methodName."&PARTNER=PayPalUK&TENDER=".$tender."&USER=".urlencode($API_UserName)."&PWD=".urlencode($API_Password)."&VENDOR=".urlencode($API_Signature).$nvpStr;
				break;
			case 'DoExpressCheckoutPayment':
				$methodName = 'D';
				$nvpreq="ACTION=".$methodName."&PARTNER=PayPalUK&TENDER=".$tender."&USER=".urlencode($API_UserName)."&PWD=".urlencode($API_Password)."&VENDOR=".urlencode($API_Signature).$nvpStr;
				break;
			case 'doDirectPayment':
				$nvpreq="TRXTYPE=S&PARTNER=PayPalUK&TENDER=".$tender."&USER=".urlencode($API_UserName)."&PWD=".urlencode($API_Password)."&VENDOR=".urlencode($API_Signature).$nvpStr;
				break;	
			case 'recurringPayment':
				$nvpreq="TRXTYPE=R&PARTNER=PayPalUK&TENDER=".$tender."&USER=".urlencode($API_UserName)."&PWD=".urlencode($API_Password)."&VENDOR=".urlencode($API_Signature).$nvpStr;
				break;	
		}
		//$nvpreq="ACTION=".$methodName."&VERSION=".urlencode($version)."&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature).$nvpStr;
		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
	
		//getting response from server
		$response = curl_exec($ch);
		//converting NVPResponse to an Associative Array
		$nvpResArray = $this->deformatNVP($response);
		$nvpReqArray = $this->deformatNVP($nvpreq);
		$_SESSION['nvpReqArray']=$nvpReqArray;
	
		if(curl_errno($ch)){
			//moving to display page to display curl errors
			$_SESSION['curl_error_no'] = curl_errno($ch) ;
			$_SESSION['curl_error_msg'] = curl_error($ch);
			$location = "perror.php";
			header("Location: $location");
		}else{
			//closing the curl
			curl_close($ch);
		}
		return $nvpResArray;
	}	
	
}
/**
 * Thanks codeIgniter for this little idea ;)
 */
if(PAYPAL_VERSION =='UK'){
	if(!class_exists('paypal')){
		eval('class paypal extends paypal_uk{}');//try to eval it just once :)
	}
}else{
	if(!class_exists('paypal')){
		eval('class paypal extends paypal_us{}');
	}
}
?>