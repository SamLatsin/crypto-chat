<?php

function escape($input, $urldecode = 0) {
    if(is_array($input)){
        foreach($input as $k=>$v){
            $input[$k]=escape($v,$urldecode);
        }
    }else{
        $input=trim($input);
        if ($urldecode == 1) {
            $input=str_replace(array('+'),array('{addplus}'),$input);
            $input = urldecode($input);
            $input=str_replace(array('{addplus}'),array('+'),$input);
        }
        if (strnatcasecmp(PHP_VERSION, '5.4.0') >= 0) {
            $input = addslashes($input);
        } else {
            if (!get_magic_quotes_gpc()) {
                $input = addslashes($input);
            }
        }
    }
    if(substr($input,-1,1)=='\\') $input=$input."'";
    return $input;
}

function encrypt($encrypt, $key) {
  $ciphering = "AES-256-CTR";
  $iv_length = openssl_cipher_iv_length($ciphering);
  $options = 0;
  $encryption_iv = '1234561891011121';
  $encoded = openssl_encrypt($encrypt, $ciphering,
            $key, $options, $encryption_iv);
  return $encoded;
}

function decrypt($decrypt, $key) {
    $ciphering = "AES-256-CTR";
    $decryption_iv = '1234561891011121';
    $options = 0;
    $decrypted=openssl_decrypt ($decrypt, $ciphering, 
            $key, $options, $decryption_iv);
    return $decrypted;
}