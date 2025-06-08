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
            <?php require_once __DIR__ . '/segments/header.php'; ?>
        </header>
        <main>
            <div class="rss-feeds-listing">
            </div>
            <div class="rss-feed">
                <script>
                    const rssFeedContainer = document.querySelector('.rss-feed');
                    const response = fetch("/scripts/rss2html.php?url=" + encodeURIComponent("https://feeds.bbci.co.uk/news/world/rss.xml") + "&fromPage=1&pageLength=5", { // dummy data for now
                        method: 'GET',
                    });
                    response.then(res => {
                        if (res.ok) {
                            return res.text();
                        }
                        throw new Error('Network response was not ok');
                    }).then(data => {
                        rssFeedContainer.innerHTML = data;
                    }).catch(error => {
                        console.error('Fetch error:', error);
                    });
                </script>
            </div>
        </main>
    </body>
</html>
