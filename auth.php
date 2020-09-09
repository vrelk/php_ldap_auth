<?php
    try{
        if(file_exists('/config/config.php')){
            include_once '/config/config.php';
        }else{
            include_once __DIR__  . '/inc_config.php';
        }

        require __DIR__ . '/vendor/autoload.php';
        require_once __DIR__  . '/inc_ldap.php';
        require_once __DIR__  . '/inc_encryption.php';
        
        if(isset($_SERVER['HTTP_X_API_KEY'])){
            if(in_array($_SERVER['HTTP_X_API_KEY'], $config['SERVICE_TOKENS'])){
                header('Remote-User: API-User', true);
                header('Remote-User-FN: NA', true);
                header('Remote-User-LN: NA', true);
                header('Remote-Groups: ', true);
                print 'OK';
                exit;
            }else{
                header("HTTP/1.0 401 Unauthorized");
                exit;
            }
        }
        
        if(isset($_COOKIE[$config['COOKIE_NAME']])){
            $decrypt = decrypt($_COOKIE[$config['COOKIE_NAME']]);
            $split = explode(':', $decrypt, 2);
            if(count($split) != 2){
                header("HTTP/1.0 401 Unauthorized");
                exit;
            }
            $user = tryLogin($split[0], $split[1]);
            
            if($user->isValid()){
                header('Remote-User: ' . $user->getUsername(), true);
                header('Remote-User-FN: ' . $user->getFirstName(), true);
                header('Remote-User-LN: ' . $user->getLastName(), true);
                header('Remote-Groups: ' . $user->getGroupsCSV(), true);
                print 'OK';
                exit;
            }else{
                header("HTTP/1.0 401 Unauthorized");
                exit;
            }
        }else{
            header("HTTP/1.0 401 Unauthorized");
            exit;
        }
    }catch(exception $e){
        header("HTTP/1.0 401 Unauthorized");
        exit;
    }