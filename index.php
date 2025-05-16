<?php
require __DIR__ . '/scripts/db.php';
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);
ini_set('session.cookie_domain', '.mateishome.page');
session_start();
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
                <div class="user-profile-container">
                    <img src="https://mateishome.page/applets/imgproxy.php?id=20" alt="User" class="user-profile-picture">
                    <div>
                        <h2>Username</h2>
                        Logged in
                    </div>
                </div>
        </header>
        <main>
            <div class="rss-feeds-listing">

            </div>
        </main>
    </body>
</html>