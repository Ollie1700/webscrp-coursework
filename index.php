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
            <div class="profile">
                <img src="img/default_user_profile_pic.png">
                <span class="name">
                    <?php
                    
                        if(isset($CURRENT_USER)) {
                            echo 'Welcome back, ' . $CURRENT_USER->get_user_first_name() . '!';
                        }
                        else {
                            include 'login.php';
                        }

                    ?>
                </span>
                <span class="description">Design Team</span>
            </div>
        </header>
        
        <section id="channel-container" class="channel-container">
            <?php if(isset($CURRENT_USER)) : include 'api/rooms.php'; else : ?>
                
                
                
            <?php endif; ?>
        </section>
        
        <script>
        </script>
    </body>
</html>