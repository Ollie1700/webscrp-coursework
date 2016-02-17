<?php if(isset($CURRENT_USER)) : ?>

    You are already logged in, <?php echo $CURRENT_USER->get_user_first_name(); ?>, no need to register.

<?php else : ?>

    <form class="big-form" id="form-register">
        <input id="form-register-email" type="text" name="email" placeholder="Email" />
        <input id="form-register-first-name" type="text" name="first_name" placeholder="First Name" />
        <input id="form-register-last-name" type="text" name="last_name" placeholder="Last Name" />
        <input id="form-register-password" type="password" name="password" placeholder="Password" />
        <input id="form-register-confirm-password" type="password" name="confirm_password" placeholder="Confirm Password" />
        <input id="form-register-submit" type="submit" name="submit" />
        
        <span id="register-form-feedback"></span>
    </form>
    
    <div id="successfully-created-account" style="display:none;">
        Your account has been created! You can now <a id="log-in-link" href="#">log in</a> with <strong id="newly-created-account-name"></strong> and your chosen password.
    </div>
    
<?php endif; ?>