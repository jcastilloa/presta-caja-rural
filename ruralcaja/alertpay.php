<?php

class alertpay extends PaymentModule
{
	private	$_html = '';
	private $_postErrors = array();

	public function __construct(){
		$this->name = 'alertpay';
		$this->tab = 'Payment';
		$this->version = 1.0;
        
		// Array config con los datos de configuración
		$config = Configuration::getMultiple(array('alertpay_CLAVE', 'alertpay_USER', 'alertpay_URL'));		

		$this->url = $config['alertpay_URL'];
		$this->clave = $config['alertpay_CLAVE'];
		$this->user = $config['alertpay_USER'];

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('alertpay');
		$this->description = $this->l('Aceptar pagos con tarjeta de crédito vía Rural Caja');
		
		// Mostrar aviso en la página principal de módulos si faltan datos de configuración.
		if ($this->clave=='' OR $this->user=='' OR $this->url=='')
		  $this->warning = $this->l('Te faltan datos a configurar el módulo de TPV Rural Caja.');
	}

	public function install()
	{
		// Valores por defecto al instalar el módulo
		if (!parent::install() OR !$this->registerHook('payment'))
			return false;
	}

	public function uninstall()
	{
	   // Valores a quitar si desinstalamos el módulo
		if (!Configuration::deleteByName('alertpay_CLAVE') or !Configuration::deleteByName('alertpay_URL')
				OR !Configuration::deleteByName('alertpay_USER') OR !parent::uninstall())
			return false;
	}

	private function _postValidation(){
	
	    // Si al enviar los datos del formulario de configuración hay campos vacios, mostrar errores.
		if (isset($_POST['btnSubmit'])){
			if (empty($_POST['clave']))
				$this->_postErrors[] = $this->l('Se requiere IPN.');
                        if (empty($_POST['url']))
				$this->_postErrors[] = $this->l('Se requiere la URL de validación.');
			if (empty($_POST['user']))
				$this->_postErrors[] = $this->l('Se requiere EMAIL');
		}
	}

	private function _postProcess(){
	    // Actualizar la configuración en la BBDD
		if (isset($_POST['btnSubmit'])){
		  Configuration::updateValue('alertpay_URL', $_POST['url']);
		  Configuration::updateValue('alertpay_CLAVE', $_POST['clave']);
		  Configuration::updateValue('alertpay_USER', $_POST['user']);
		}
		
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Configuración actualizada').'</div>';
	}

	private function _displayalertpay()
	{
	    // Aparición el la lista de módulos
		$this->_html .= '<img src="../modules/alertpay/alertpay.png" style="float:left; margin-right:15px;"><b>'.$this->l('Este módulo te permite aceptar pagos con tarjeta.').'</b><br /><br />
		'.$this->l('Si el cliente elije este modo de pago, podrá pagar de forma automática.').'<br /><br />';
	}

/*'<INPUT type="text" name="url" size="50" value="'.htmlentities(Tools::getValue('url', $this->user), ENT_COMPAT, 'UTF-8').'">'*/

	private function _displayForm(){
	  
	    // Mostar formulario
		$this->_html .=
		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Configuración del TPV Rural Caja').'</legend>
				<table border="0" width="680" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Por favor completa la información requerida').'.<br /><br /></td></tr>
					<tr><td width="215" style="height: 35px;">'.$this->l('URL Validaci&oacute;n').'</td><td><INPUT type="text" name="url" size="80" value="'.htmlentities(Tools::getValue('url', $this->url), ENT_COMPAT, 'UTF-8').'"><br> Ej: https://tpv02.cajarural.com/tpv_test_portal/tpv/jsp/tpvjp_validaComercio.jsp</td></tr>
					<tr><td width="215" style="height: 35px;">'.$this->l('IPN').'</td><td><input type="text" name="clave" value="'.Tools::getValue('clave', $this->clave).'" style="width: 200px;" /></td></tr>
					<tr><td width="215" style="height: 35px;">'.$this->l('Email').'</td><td><input type="text" name="user" value="'.htmlentities(Tools::getValue('user', $this->user), ENT_COMPAT, 'UTF-8').'" style="width: 200px;" /> (CIP Pruebas: 111111111111)</td></tr>
					</td></tr>
					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Guardar configuración').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}	

	public function getContent()
	{
	    // Recoger datos
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		if (!empty($_POST))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
			$this->_html .= '<br />';
		$this->_displayalertpay();
		$this->_displayForm();
		return $this->_html;
	}

        public function zerofill($num,$zerofill) {
               while (strlen($num)<$zerofill) {
                 $num = "0".$num;
        }
        return $num;
        }         

	protected function session() {
	
		// Start session if needed
		if(!session_id()) {
			session_start();
		}
		
	}
	
	public function hookPayment($params){
		
        // Variables necesarias de fuera		
	       global $smarty, $cookie, $cart;
               include ("clase_sha1.txt");
					
	    // Valor de compra				
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));			
		$currency = new Currency(intval($id_currency));			
		$importe = number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency), 2, '.', '');
		$importe = str_replace('.','',$importe);              
					
		// El número de pedido es el ID del carrito.
		$alertpay_order_ref = intval($params['cart']->id);
                $alertpay_order_ref = $this->zerofill($alertpay_order_ref, 8);

		$config_result = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/alertpay/validation.php';
		$user  = Tools::getValue('user', $this->user);
		$clave = Tools::getValue('clave', $this->clave);
		
		// Cálculo Firma
		//$alertpay_firma = md5($user.$alertpay_order_ref.$importe.md5($clave));
		
		$products = $params['cart']->getProducts();
		$alertpay_clientconcept = '';

		foreach ($products as $product) {
			$alertpay_clientconcept .= $product['quantity'].' '.$product['name']."<br>";
		}

                $fecha = time();
                $fechaf = date("ymdhis", $fecha);

               $moneda = 'EUR';
               $cip = $user;
               $numpedido = $alertpay_order_ref;

               $fuc = $clave;

               $sha = new SHA;
               $message = $fechaf.$cip.$importe.$moneda.$numpedido;
               $digest1 = $sha->hash_string($message);
               $alertpay_firma = $sha->hash_to_string($digest1);

               $this->session();
               $_SESSION["fechaf"] = $fechaf;
               $_SESSION["cip"] = $cip;
			   $_SESSION["fuc"] = $fuc;
               $_SESSION["numpedido"] = $numpedido;
               $_SESSION["importe"] = $importe;


		$smarty->assign(array(
			'url' => Tools::getValue('url', $this->url),
			'importe' => $importe,
			'alertpay_order_ref' => $alertpay_order_ref,
			'alertpay_concept' => ($cookie->logged ? $cookie->customer_firstname.' '.$cookie->customer_lastname : false),
			'fuc' =>  $fuc /*Tools::getValue('user', $this->user)*/,
			'config_result' => $config_result,            
			'alertpay_clientconcept' => $alertpay_clientconcept,
			'config_recibo' => 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/alertpay/recibo.php',
			'alertpay_firma' => $alertpay_firma,
                        'alertpay_fecha' => $fechaf,
                        'alertpay_fechao' => $fecha,
                        'cip' => $cip,
                        'moneda' => $moneda,									
			'this_path' => $this->_path
		));
		return $this->display(__FILE__, 'alertpay.tpl');
    }
}
?>