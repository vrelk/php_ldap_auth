<?php
    if(file_exists('/config/config.php')){
        include_once '/config/config.php';
    }else{
        include_once __DIR__  . '/inc_config.php';
    }

    require __DIR__ . '/vendor/autoload.php';
    require_once __DIR__  . '/inc_ldap.php';
    
    if(isset($_GET['logout'])){
        $options = array(
            'expires' => time() - 3600,
            'domain' => $config['COOKIE_DOMAIN'],
            'secure' => $config['COOKIE_SECURE'],
            'httponly' => true,
            'path' => '/',
        );
        setcookie($config['COOKIE_NAME'], '', $options);
        header('Location: /');
        exit;
    }
    
    $user = NULL;
    
    if(!empty($_POST['username']) && !empty($_POST['password'])){
        $rememberme = (empty($_POST['rememberme']) ? 0 : 30);
        $user = tryLogin($_POST['username'], $_POST['password']);
        
        if($user->isValid()){
            generateCookie($_POST['username'], $_POST['password'], $rememberme);
            header('Location: /');
            exit;
        }
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=UTF-8">
        <title>Log In</title>
        <style type="text/css" rel="stylesheet">
            body { background-color: #f1f1f1; font-family: sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif; }
            .log-in { width: 400px; height: 500px; position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: auto; background-color: #fff; border-radius: 3px; overflow: hidden; -webkit-box-shadow: 0px 0px 2px 0px rgba(222,222,222,1); -moz-box-shadow: 0px 0px 2px 0px rgba(222,222,222,1); box-shadow: 0px 0px 2px 0px rgba(222,222,222,1); }
            .log-in > div { position: relative; }
            .log-in .content { margin-top: 50px; padding: 20px; text-align: center; }
            h1, h2 { text-align: center; }
            h1 {  margin-top: 20px; margin-bottom: 20px; letter-spacing: -0.05rem; color: #565656; font-size: 1.6rem; }
            form { margin-top: 50px; }
            input[type="text"], input[type="password"] { width: 80%; padding: 10px; border-top: 0; border-left: 0; border-right: 0; outline: none; }
            input[type="text"]:focus, input[type="password"]:focus { border-bottom: 2px solid #666; }
            button { width: 80%; padding: 10px; background-color: #3468e2; border: none; color: #fff; cursor: pointer; margin-top: 50px; }
            button:hover { background-color: #5581e8; }
        </style>
    </head>
    <body>
        <div class="log-in">
            <div class="content">
                <h1>Log in to your account</h1>
                <form action="<?php echo explode('?', $_SERVER['REQUEST_URI'], 2)[0];?>" method="post">
                    <p>
                        <input type="text" name="username" placeholder="Username" aria-label="Username" />
                    </p>
                    <p>
                        <input type="password" name="password" placeholder="Password" aria-label="Password" />
                    </p>
                    <p>
                        Remember Me <input type="checkbox" name="rememberme" aria-label="Remember Me" />
                    </p>
                    <!-- <p>
                        <input type="text" name="token" placeholder="2FA Token" aria-label="2FA Token" />
                    </p> -->
                    <input type="hidden" name="target" value="TARGET">
                    <?php if($user != NULL){ if(!$user->isValid()){ echo "<p style='color: red; font-weight: bold;'>{$user->getError()}</p>";}}?>
                    <button type="submit" class="submit btn btn-primary">Log In</button>
                </form>
            </div>
        </div>
    </body>
</html>