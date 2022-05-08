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

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        return false;
        // throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

function unaccent($string)
{
    return preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
}

function translit($s) {
    $s = (string) $s; 
    $s = strip_tags($s); 
    $s = str_replace(array("\n", "\r"), " ", $s); 
    $s = str_replace("/", "-", $s); 
    $s = preg_replace("/\s+/", ' ', $s); 
    $s = trim($s); 
    $s = function_exists('mb_strtolower') ? mb_strtolower($s,'UTF-8') : strtolower($s,'UTF-8'); 
    $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'ju','я'=>'ja','ъ'=>'','ь'=>''));
    $s = str_replace(" ", "-", $s); 
    $s = unaccent($s);
    return $s; 
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