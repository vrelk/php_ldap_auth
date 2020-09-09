<?php
    require_once __DIR__  . '/inc_classes.php';
    require_once __DIR__  . '/inc_encryption.php';
    
    function tryLogin($username, $password){
        global $config;
        // Construct new Adldap instance.
        $ad = new \Adldap\Adldap();

        // Create a configuration array.
        $ldapconfig = [  
          'hosts'    => $config['AD_SERVERS'],
          'timeout' => $config['AD_TIMEOUT'],
          'follow_referrals' => $config['AD_FOLLOW_REFERRALS'],
          'account_suffix' => $config['AD_ACCOUNT_SUFFIX'],
          'base_dn'  => $config['AD_BASE_DN'],
        ];
        switch(strtoupper($config['AD_SERVER_ENCRYPTION'])){
            case 'SSL':
                $ldapconfig['use_ssl'] = true;
                break;
            case 'TLS':
                $ldapconfig['use_tls'] = true;
                break;
        }

        // Add a connection provider to Adldap.
        $ad->addProvider($ldapconfig);

        try {
            // If a successful connection is made to your server, the provider will be returned.
            $provider = $ad->getDefaultProvider();
            $provider->auth()->attempt($username, $password, $bindAsUser = true);
            
            $search = $provider->search();

            $user = $search->select(['samaccountname', 'memberof', 'givenname', 'sn'])->findBy('samaccountname', $username);
            
            if($user == NULL){
                $userObj = AdUser::newError($username, "Invalid username/password.", NULL);
                return $userObj;
            }
            
            $groups = array();
            foreach($user->getMemberOf() as $group){
                $tmpGrp = new AdGroup($group);
                if($tmpGrp->isValid()){
                    $groups[] = $tmpGrp;
                }
            }
            
            $userObj = AdUser::newValid($username, $user->getFirstName(), $user->getLastName(), $groups);
            return $userObj;
            
        } catch (\Adldap\Auth\BindException $e) {
            // There was an issue binding / connecting to the server.
            $userObj = AdUser::newError($username, $e->getDetailedError()->getErrorMessage(), $e->getDetailedError()->getDiagnosticMessage());
            return $userObj;
        }
    }
    
    function generateCookie($username, $password, $expire = 0){
        global $config;
        if($expire != 0){
            $expire = time() + 86400 * $expire;
        }
        
        $encrypt = encrypt("{$username}:{$password}");
        
        $options = array(
            'expires' => $expire,
            'domain' => $config['COOKIE_DOMAIN'],
            'secure' => $config['COOKIE_SECURE'],
            'httponly' => true,
            'path' => '/',
        );
        setcookie($config['COOKIE_NAME'], $encrypt, $options);
    }