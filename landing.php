<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once __DIR__ . '/scripts/createHeadSection.php';
        createHeadSection(); ?>
    </head>
    <body>
        <header>
            <?php require_once __DIR__ . '/segments/header.php'; ?>
        </header>
        <main>
            <div class="rss-feeds-listing">
                <div class="row" id="welcome">
                    <img src="" alt="Welcome icon" class="feed-icon">
                    <h2>Welcome</h2>
                </div>
            </div>
            <div class="rss-feed">
                <div class="large-container">
                    <div class="vertical-flex-align-center">
                        <img src="/assets/images/logo.png" alt="mRSS logo" width="300" class="logo">
                        <h1>Welcome to mRSS!</h1>
                    </div>
                    <p>mRSS is an easy-to-use, simple, and customisable RSS reader made in PHP. It was developed by <a href="https://mateishome.page/">Matei "sHomePage"</a> for <a href="https://hack.club/">Hack Club</a>'s Neighborhood event. You can find the source code on my <a href="https://github.com/coolbeans1212/mRSS">GitHub</a>.</p>
                    <p>To learn how to use the mRSS application, click on one of the sidebar options on the left. I recommend going in order from top to bottom. If you want to return to this welcome tour at any time, enter <a href="/landing.php"><?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></a> into the search box near the
                    top of your web browser window.</p>
                    <p class="small-text">mRSS is licensed under the <a href="https://www.gnu.org/licenses/agpl-3.0.txt">AGPL-3.0</a> license, meaning you can use and edit mRSS freely on your own copy, but you cannot use it on a public-facing website without making the source code available.</p>
                </div>
            </div>