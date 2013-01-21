<?php
/**
 * 
 * EPDDecryptor
 * 
 * A class which allows you to decrypt AlertPay's Encrypted Payment Details (EPD).
 * 
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY
 * OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE IMPLIED WARRANTIES OF FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * @author AlertPay
 * @copyright 2010
 */
 
class EPDDecryptor {
	
    /**
     * The IPN security code that is generated from your AlertPay account
     * which is used as the key to decrypt the cypher text.
     */	
	private $key;

    /**
     * One of the MCRYPT_ciphername constants of the name of the 
	 * algorithm as string
     */
	private $cipher;
	
    /**
     * One of the MCRYPT_MODE_modename constants
     */	
	private $mode;
    
    /**
    * The iv parameter used for the initialisation in CB
    */
    private $iv;
 
    /**
    * A variable holding the parsed IPN variables
    */    
	private $ipnData;
    
    /**
     * EPDDecryptor::__construct()
     * 
     * Constructs a EPDDecryptor object and sets the
     * necessary variables
     * 
     */	
    public function __construct($securityCode)
    {
    	$this->key = $securityCode;
        $this->iv = 'alertpay';
		$this->cipher = MCRYPT_3DES;
		$this->mode = MCRYPT_MODE_CBC;
    }
		
    /**
     * EPDDecryptor::decrypt()
     * 
     * Decrypts the given text using the key provided.
     * 
     * @param string $key The key used to decrypt.
     * @param string $data The encrypted text.
     * 
     * @return string The decrypted text.
     */	
	public function decrypt($cypherText)
	{    
 		//Decode the base64 encoded text		
		$cypherText = base64_decode($cypherText);
               
        //Complete the key
		$key_add = 24-strlen($this->key);
		$this->key .= substr($this->key,0,$key_add);
                
        // use mcrypt library for encryption
        $decryptedText = mcrypt_decrypt($this->cipher, $this->key, $cypherText, $this->mode, $this->iv);
        parse_str(trim($decryptedText,"\x00..\x1F"),$this->ipnData);
               		
		return $this->ipnData;
	}
}

?>