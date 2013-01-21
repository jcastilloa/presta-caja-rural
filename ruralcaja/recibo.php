<?

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/alertpay.php');

$id = $_GET["id"];
$alertpay = new alertpay();


// Start session if needed
		if(!session_id()) {
			session_start();
		}


$order = new Order($alertpay->currentOrder);
Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$_REQUEST['alertpay_order_ref'].'&id_module='.$alertpay->id.'&id_order='.$alertpay->currentOrder.'&key='.$order->secure_key);
?>