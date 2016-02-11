<?php

require_once 'api/init.php';

$errors = array();
$success = false;

$email = "";
$first_name = "";
$last_name = "";
$password = "";
$confirm_password = "";

if(isset($_REQUEST['submit'])) {
    
    // Gather all of the variables
    $email = $_REQUEST['email'];
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $password = $_REQUEST['password'];
    $confirm_password = $_REQUEST['confirm_password'];
    
    // Validate email 
    if(!empty($email)) {
        
        // Check if email is valid
        $valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if($valid_email) {
            
            // Validate names
            if(!empty($first_name) && !empty($last_name)) {
                
                // Validate passwords
                if(!empty($password) && !empty($confirm_password)) {
                    
                    // Check that passwords match
                    if($password === $confirm_password) {
                        
                        // Everything is valid, we're good to go
                        $success = User::create($email, $first_name, $last_name, $password);
                        
                    }
                    else {
                        $errors[] = "Your passwords do not match.";
                    }
                    
                }
                else {
                    $errors[] = "You must type a password and then confirm it.";
                }
                
            }
            else {
                $errors[] = "First and last name are required.";
            }
            
        }
        else {
            $errors[] = "Please enter a valid email address.";
        }
        
    }
    else {
        $errors[] = "Email field is required.";
    }
    
}

?>

<?php if(isset($CURRENT_USER)) : ?>

    You are already logged in, <?php echo $CURRENT_USER->get_user_first_name(); ?>, no need to register.

<?php elseif(!$success) : ?>

    <form action="" method="POST">
        <input type="text" name="email" placeholder="Email" value="<?php echo $email; ?>" />
        <input type="text" name="first_name" placeholder="First Name" value="<?php echo $first_name; ?>" />
        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $last_name; ?>" />
        <input type="password" name="password" placeholder="Password" />
        <input type="password" name="confirm_password" placeholder="Confirm Password" />
        <input type="submit" name="submit" />

        <?php if(!empty($errors)) : ?>
            
            <span style="font-weight:bold;">Please fix the following errors:</span>
            <ul style="color:red;">

                <?php foreach($errors as $error) : ?>

                    <li><?php echo $error; ?></li>

                <?php endforeach; ?>

            </ul>

        <?php endif; ?>

    </form>

<?php else : ?>
    
    Your account has been created! You can now <a href="login.php">log in</a> with <strong><?php echo $email; ?></strong> and your chosen password.
    
<?php endif; ?>