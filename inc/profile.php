<?php require_once 'api/init.php'; ?>

<?php if($CURRENT_USER) : ?>

    <h1>Your Profile</h1>

    <p>
        Here you can edit any element of your profile and manage your rooms and friends.
    </p>
    
    <h2>Account Details</h2>
    
    <form id="update-profile-form">
        <table>
            <tr>
                <td style="width: 80%;">
                    <table>
                        <tr>
                            <td>
                                Email:
                            </td>
                            <td>
                                <input id="update-profile-form-email" type="text" name="email" placeholder="Email" value="<?php echo $CURRENT_USER->get_user_email(); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                First Name:
                            </td>
                            <td>
                                <input id="update-profile-form-first-name" type="text" name="first_name" placeholder="First Name" value="<?php echo $CURRENT_USER->get_user_first_name(); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Last Name:
                            </td>
                            <td>
                                <input id="update-profile-form-last-name" type="text" name="last_name" placeholder="Last Name" value="<?php echo $CURRENT_USER->get_user_last_name(); ?>" />
                            </td>
                        </tr>
                    </table>
                    <input id="update-profile-user-id" type="hidden" value="<?php echo $CURRENT_USER->get_user_id(); ?>" />
                    <input id="submit-update-profile-form" type="submit" name="submit" value="Update Profile" />
                </td>
                <td style="width: 20%;">
                    Profile pic
                </td>
            </tr>
        </table>
    </form>

    <span id="update-profile-form-feedback" style="color:red;"></span>

    <h2>Friends</h2>

    <span id="add-friend-form-feedback" style="color:red;"></span>
    
    <form class="inline-form" id="add-friend-form">
        Add a friend: <input id="add-friend-input" type="text" placeholder="Friend's Email" />
        <input id="add-friend-submit" type="submit" value="Send friend request" />
        <input id="add-friend-user-id" type="hidden" value="<?php echo $CURRENT_USER->get_user_id(); ?>" />
    </form>

    <div id="friends-list-container"><table id="friends-list"></table></div>

    <h2>Rooms</h2>
    
    <form id="join-room-form">
        <input id="join-room-input-room" type="text" placeholder="Room name" />
        <input id="join-room-input-submit" type="submit" value="Create/Join room" />
    </form>

    <p>
        You are a member of the following rooms:
    </p>

    <span id="user-rooms-info"></span>

    <div id="user-rooms-list"></div>

<?php else : ?>

    You must be logged in to view your profile. Please <a href="login.php">log in</a> or <a href="register.php">register</a>.

<?php endif; ?>

















