<?php

    function generate_string($strength = 64) {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }

    function genIV(){
        $ivlen = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivlen);
        return base64_encode($iv);
    }


    echo "<html><head></head><body><pre>";
    echo "A randomly generated string that can be used for SECRET or an entry for SERVICE_TOKENS: " . generate_string(64) . "\n";
    echo "A randomly generated value that can be used for SECRET_HASH: " . genIV();
    echo "</pre></body></html>";