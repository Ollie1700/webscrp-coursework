<?php

require_once 'api/init.php';

$errors = array();
$success = false;

if(isset($_REQUEST['submit'])) {
    
    // Gather all of the variables
    $email = $_REQUEST['email'];
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    
    // Validate email 
    if(!empty($email)) {
        
        // Check if email is valid
        $valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if($valid_email) {
            
            // Validate names
            if(!empty($first_name) && !empty($last_name)) {

                // Everything is valid, we're good to go
                $success = User::update($_SESSION['user_id'], $email, $first_name, $last_name);

            }
            else {
                $errors[] = "You cannot leave your first and last name blank!";
            }
            
        }
        else {
            $errors[] = "Please enter a valid email address.";
        }
        
    }
    else {
        $errors[] = "You cannot leave your email blank!";
    }
    
}

?>

<?php if($CURRENT_USER) : ?>

    <h1>Your Profile</h1>

    <form action="" method="POST">
        <input type="text" name="email" placeholder="Email" value="<?php echo $success ? $email : $CURRENT_USER->get_user_email(); ?>" />
        <input type="text" name="first_name" placeholder="First Name" value="<?php echo $success ? $first_name : $CURRENT_USER->get_user_first_name(); ?>" />
        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $success ? $last_name : $CURRENT_USER->get_user_last_name(); ?>" />
        <input type="submit" name="submit" value="Update Profile" />
    </form>

    <?php if(!empty($errors)) : ?>

        <span style="font-weight:bold;">Please fix the following errors:</span>
        <ul style="color:red;">

            <?php foreach($errors as $error) : ?>

                <li><?php echo $error; ?></li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>

    <?php if($success) : ?>
        Your profile has been updated successfully!
    <?php endif; ?>

<?php else : ?>

    You must be logged in to view your profile. Please <a href="login.php">log in</a> or <a href="register.php">register</a>.

<?php endif; ?>