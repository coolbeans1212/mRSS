<?php
$dbs = require __DIR__ . '/scripts/db.php';
$userdb = $dbs['userdb'];
$mrssdb = $dbs['mrssdb'];
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);
ini_set('session.cookie_domain', '.mateishome.page');
session_start();

if (isset($_SESSION['user_id'])) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $userdb->prepare($query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once __DIR__ . '/scripts/createHeadSection.php';
        createHeadSection(); ?>
    </head>
    <body>
        <header>
            <a href="/">
                <img src="/assets/images/logo.png" alt="mRSS Logo" class="fitheader show-only-on-desktop">
                <img src="/assets/images/mrss-favicon.ico" alt="mRSS Logo" class="fitheader show-only-on-mobile">
            </a>
            <input type="text" id="search" placeholder="Search your feeds..." class="search" autocomplete="off">
            <div class="horizontal-flex-align-center">
                <div class="vertical-flex-align-center">
                    <img src="/assets/images/cog.png" alt="Settings" height="50px" width="50px" >
                    Settings
                </div>
                <div class="vertical-flex-align-center">
                    <div class="user-profile-container">
                        <?php if (isset($userInfo)) { ?>
                        <img src="https://mateishome.page/applets/imgproxy.php?id=<?php echo $userInfo['pfp'] ?? 20; ?>" alt="User" class="user-profile-picture">
                        <?php } else { ?>
                        <img src="/assets/images/guestAccount.png" alt="User" class="user-profile-picture">
                        <?php } ?>
                        <div>
                            <?php
                            echo '<h2>'; echo $userInfo['username'] ?? 'Guest'; echo '</h2>';
                            if (isset($userInfo)) {
                                echo 'Logged in';
                            } else {
                                echo 'Temporary account';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="profile-dropdown" style="display: none;">
                        <?php if (isset($userInfo)) { ?>
                        <a href="/account/logout.php">Logout</a>
                        <?php } else { ?>
                        <a href="/account/login.php">Login</a>
                        <a href="/account/register.php">Register</a>
                        <?php } ?>
                </div>
        </header>
        <main>
            <div class="rss-feeds-listing">

            </div>
        </main>
        <script>
            const userProfileContainer = document.querySelector('.user-profile-container');
            const profileDropdown = document.querySelector('.profile-dropdown');
            userProfileContainer.addEventListener('click', () => {
                if (profileDropdown.style.display === 'none') {
                    profileDropdown.style.display = '';
                } else {
                    profileDropdown.style.display = 'none';
                }
            });
            // close the dropdown if the user clicks somewhere else
            document.addEventListener('click', (event) => {
                if (!userProfileContainer.contains(event.target) && !profileDropdown.contains(event.target)) {
                    profileDropdown.style.display = 'none';
                }
            });
        </script>
    </body>
</html>