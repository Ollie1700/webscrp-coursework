<?php require_once 'api/init.php'; ?>

<?php if($CURRENT_USER) : ?>

    <h1>Your Profile</h1>

    <p>
        Here you can edit any element of your profile and manage your rooms and friends.
    </p>
    
    <h2>Account Details</h2>
    
    <form id="update-profile-form">
        <input id="update-profile-form-email" type="text" name="email" placeholder="Email" value="" />
        <input id="update-profile-form-first-name" type="text" name="first_name" placeholder="First Name" value="" />
        <input id="update-profile-form-last-name" type="text" name="last_name" placeholder="Last Name" value="" />
        <input id="update-profile-user-id" type="hidden" value="<?php echo $CURRENT_USER->get_user_id(); ?>" />
        <input id="submit-update-profile-form" type="submit" name="submit" value="Update Profile" />
    </form>

    <span id="update-profile-form-errors" style="color:red;"></span>

    <h2>Friends</h2>
    
    
    
    <h2>Rooms</h2>

<?php else : ?>

    You must be logged in to view your profile. Please <a href="login.php">log in</a> or <a href="register.php">register</a>.

<?php endif; ?>

















