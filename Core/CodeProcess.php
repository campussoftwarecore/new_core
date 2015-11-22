<?php
    class Core_CodeProcess 
    {
        public function stripslashes_deep($value)
	{
		$value = is_array($value) ?array_map('stripslashes_deep', $value) :stripslashes($value);
	
		return $value;
	}
        public function keyExistsInArray($key,$array=  array())
        {
            if(is_array($array))
            {                
                return array_key_exists($key,$array);
            }
            else
            {
                return false;
            }
        }
        public function convertEncryptDecrypt($action,$string)
        {
            $output = false;
	
	    $encrypt_method = "AES-256-CBC";
	    $secret_key = ENCRYPTION_KEY;
	    $secret_iv = ENCRYPTION_KEY;
	
	    // hash
	    $key = hash('sha256', $secret_key);
	    
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	
	    if( $action == 'encrypt' ) 
            {
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
	    }
	    else if( $action == 'decrypt' )
            {
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }
	
	    return $output ;
        }
    }
?>
