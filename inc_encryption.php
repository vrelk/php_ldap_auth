<?php
    if(file_exists('/config/config.php')){
        include_once '/config/config.php';
    }else{
        include_once __DIR__  . '/inc_config.php';
    }
    
    function encrypt($string){
        global $config;
        $method = 'aes-256-cbc';
        $iv = base64_decode($config['SECRET_HASH']);
        
        $encrypted = base64_encode(openssl_encrypt($string, $method, $config['SECRET'], OPENSSL_RAW_DATA, $iv));
        return $encrypted;
    }
    
    function decrypt($string){
        global $config;
        $method = 'aes-256-cbc';
        $iv = base64_decode($config['SECRET_HASH']);
        
        $decrypted = openssl_decrypt(base64_decode($string), $method, $config['SECRET'], OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }