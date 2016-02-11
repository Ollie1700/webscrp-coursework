<?php require_once 'api/init.php'; ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Yapper! Instant messaging for teams</title>
        
        <link rel="stylesheet" href="style.css" />
        
        <script src="lib/ajax.js"></script>
        
    </head>
    <body>
        <header>
            <div class="logo">
                <h1>
                    Yapper!
                    <span class="subtitle">Instant messaging for teams</span>
                </h1>
            </div>
            
            <?php if(isset($CURRENT_USER)) : ?>
                <div class="profile">
                    <img src="img/default_user_profile_pic.png">
                    <span class="name">
                        <?php echo 'Welcome back, ' . $CURRENT_USER->get_user_first_name() . '!'; ?>
                    </span>
                    <span class="description">Design Team</span>
                </div>
            <?php else : ?>
            <?php endif; ?>
        </header>
        
        <section id="channel-container" class="channel-container">
            <?php if(isset($CURRENT_USER)) : include 'api/rooms.php'; else : ?>
                
                <div class="user-login-container">
                    
                    <h1>Welcome! Please <a id="log-in-form-toggle" href="#">log in</a> or <a id="register-form-toggle" href="#">register</a>.</h1>
                    
                    <div id="user-login-form-container">
                        <?php include 'inc/login.php'; ?>
                    </div>
                    <div id="user-register-form-container">
                        <?php include 'inc/register.php'; ?>
                    </div>
                    
                </div>
                
            <?php endif; ?>
        </section>
        
        <?php if(!isset($CURRENT_USER)) : ?>
            <script src="lib/user.js"></script>
        <?php endif; ?>
    </body>
</html>













