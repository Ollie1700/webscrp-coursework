<?php

require_once 'api/init.php';

$login = true;

if(isset($_REQUEST['submit']) && isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
    $login = User::login($_REQUEST['email'], $_REQUEST['password']);
    if(isset($_SESSION['user_id'])) {
        $CURRENT_USER = User::get($_SESSION['user_id']);
    }
}

?>

<?php if(!isset($CURRENT_USER)) : ?>

    <form action="" method="POST">
        <input type="text" name="email" value="<?php echo isset($_REQUEST['email']) ? $_REQUEST['email'] : ''; ?>" placeholder="Email" />
        <input type="password" name="password" placeholder="Password" />
        <input type="submit" name="submit" value="Log In" />
        <?php if(!$login) : ?>
            <span style="color:red;">Login failed!</span>
        <?php endif; ?>
    </form>

<?php else : ?>
    
    Welcome back, <?php echo $CURRENT_USER->get_user_first_name(); ?>!
    
<?php endif; ?>