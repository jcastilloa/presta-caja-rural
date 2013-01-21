<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/alertpay.php');

//include ("clase_sha1.txt");

if (!empty($_GET)){

        if(!session_id()) {
	    session_start();
	}

	$sfechaf = $_SESSION["fechaf"];
	$scip = $_SESSION["cip"];
	$sfuc = $_SESSION["fuc"];
	$snumpedido = $_SESSION["numpedido"];
	$simporte = $_SESSION["importe"];
	
//reachdharmesh@gmail.com
//pwd: AlertPay
//PIN: 2511
//IPN: XYJDGSaL0jQqvN4R
	
	define("IPN_SECURITY_CODE", $scip);
	define("MY_MERCHANT_EMAIL", $sfuc);

	// Creamos objeto
	$alertpay = new alertpay();

        $importe  = $_REQUEST['importe'];
		/*$order_ref = $_REQUEST['numpedido'];
		$result = $_REQUEST['compra'];
        $firma = $_REQUEST['firma'];*/

        $vimporte  = number_format($_REQUEST['importe'] / 100,2);

        //AQUI PONER LA VALIDACION DE LA COMPRA ...
		
		$receivedSecurityCode = urldecode($_POST['ap_securitycode']);
		$receivedMerchantEmailAddress = urldecode($_POST['ap_merchant']);	
		$transactionStatus = urldecode($_POST['ap_status']);
		$testModeStatus = urldecode($_POST['ap_test']);	
        $totalAmountReceived = urldecode($_POST['ap_totalamount']); 		
		
		if ($receivedMerchantEmailAddress != MY_MERCHANT_EMAIL) {
		// The data was not meant for the business profile under this email address.
		// Take appropriate action 
		} else {	
		//Check if the security code matches
		if ($receivedSecurityCode != IPN_SECURITY_CODE) {
			// The data is NOT sent by AlertPay.
			// Take appropriate action.
			
		   $valido=0;          
           Tools::redirectLink(__PS_BASE_URI__.'history.php');
		}
		else {
			if ($transactionStatus == "Success") {
				if ($testModeStatus == "1") {
					// Since Test Mode is ON, no transaction reference number will be returned.
					// Your site is currently being integrated with AlertPay IPN for TESTING PURPOSES
					// ONLY. Don't store any information in your production database and 
					// DO NOT process this transaction as a real order.
				}
				else {
					// This REAL transaction is complete and the amount was paid successfully.
					// Process the order here by cross referencing the received data with your database. 														
					// Check that the total amount paid was the expected amount.
					// Check that the amount paid was for the correct service.
					// Check that the currency is correct.
					// ie: if ($totalAmountReceived == 50) ... etc ...
					// After verification, update your database accordingly.					
					$valido=1;
                    $alertpay->validateOrder($order_ref, _PS_OS_PAYMENT_, $totalAmountReceived, $alertpay->displayName);
                    Tools::redirectLink(__PS_BASE_URI__.'history.php');
				}			
			}
			else {
					// Transaction was cancelled or an incorrect status was returned.
					// Take appropriate action.
					           $valido=0;          
                               Tools::redirectLink(__PS_BASE_URI__.'history.php');
			}
		}
	}
		
		
} else {
  echo "Vacio";
}

?>
