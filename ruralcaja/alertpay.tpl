<p class="payment_module">
	<a href="javascript:$('#alertpay_form').submit();" title="{l s='Conectar con el TPV' mod='alertpay'}">
		<img src="{$module_dir}visa.png" alt="{l s='Conectar con el TPV' mod='alertpay'}" />
		{l s='Pago con tarjeta de crédito' mod='alertpay'}
	</a>
</p>


<form method="post" action="https://www.alertpay.com/PayProcess.aspx" >
    <input type="hidden" name="ap_merchant" value="{$fuc}"/>
    <input type="hidden" name="ap_purchasetype" value="item-goods"/>
    <input type="hidden" name="ap_itemname" value="{$alertpay_order_ref}"/>
	<input type="hidden" name="ap_amount" value="{$importe}"/>
    <input type="hidden" name="ap_currency" value="EUR"/>    
    <input type="hidden" name="ap_returnurl" value="{$config_result}"/>
    <input type="hidden" name="ap_cancelurl" value="{$config_result}"/>
    <input type="hidden" name="apc_1" value="Blue"/>    
    <input type="image" src="https://www.alertpay.com//PayNow/4F59239578EA46C1AD168BA6E9BD2067g.gif"/>
</form>


<!--

P:{$alertpay_order_ref}
{$config_result}
fecha: {$alertpay_fecha}
fuc: {$fuc}
cip: {$cip}


<form method="post" action="{$url}" class="hidden" id="alertpay_form">
<input type="hidden" name="importe" value="{$importe}">
<input type="hidden" name="moneda" value="{$moneda}">
<input type="hidden" name="numpedido" value="{$alertpay_order_ref}">
<input type="hidden" name="fuc" value="{$fuc}">
<input type="hidden" name="idterminal" value="001">
<input type="hidden" name="idioma" value="0">
<input type="hidden" name="fecha" value="{$alertpay_fecha}">
<input type="hidden" name="cip" value="{$cip}">
<input type="hidden" name="url" value="{$config_result}">
<input type="hidden" name="firma" value="{$alertpay_firma}">
</form>
-->
