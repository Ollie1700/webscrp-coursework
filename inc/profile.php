<?php require_once 'api/init.php'; ?>

<?php if($CURRENT_USER) : ?>

    <h1>Your Profile</h1>

    <p>
        Here you can edit any element of your profile and manage your rooms and friends.
    </p>
    
    <h2>Account Details</h2>
    
    <form id="update-profile-form">
        <input id="update-profile-form-email" type="text" name="email" placeholder="Email" value="<?php echo $CURRENT_USER->get_user_email(); ?>" />
        <input id="update-profile-form-first-name" type="text" name="first_name" placeholder="First Name" value="<?php echo $CURRENT_USER->get_user_first_name(); ?>" />
        <input id="update-profile-form-last-name" type="text" name="last_name" placeholder="Last Name" value="<?php echo $CURRENT_USER->get_user_last_name(); ?>" />
        <input id="update-profile-user-id" type="hidden" value="<?php echo $CURRENT_USER->get_user_id(); ?>" />
        <input id="submit-update-profile-form" type="submit" name="submit" value="Update Profile" />
    </form>

    <span id="update-profile-form-feedback" style="color:red;"></span>

    <h2>Friends</h2>
    
    <h3>Add a friend:</h3>
    
    <form id="add-friend-form">
        <input id="add-friend-input" type="text" placeholder="Friend's Email" />
        <input id="add-friend-submit" type="submit" value="Send friend request" />
    </form>

    <h2>Rooms</h2>

<?php else : ?>

    You must be logged in to view your profile. Please <a href="login.php">log in</a> or <a href="register.php">register</a>.

<?php endif; ?>

















