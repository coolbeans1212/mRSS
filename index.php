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

    $query = "SELECT * FROM user_preferences WHERE id = ?";
    $stmt = $mrssdb->prepare($query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        createUserPreferences();
        $result = $stmt->get_result();
    }
    $userPreferences = $result->fetch_assoc();
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
            </div>
                <script>
                    let rssFeedContainer = document.querySelector('.rss-feed');
                    let feedURL = encodeURIComponent("https://feeds.bbci.co.uk/news/world/rss.xml");
                    function attachPaginationListeners() {
                        document.querySelectorAll('.different-page').forEach(page => {
                            page.addEventListener('click', () => {
                                let url = new URL(window.location.href);
                                url.searchParams.set('fromPage', page.id);
                                // Instead of reloading, fetch new page content!
                                fetch("/scripts/rss2html.php?url=" + feedURL + "&fromPage=" + page.id + "&pageLength=<?php echo $userPreferences['itemsPerPage']?>")
                                    .then(res => res.text())
                                    .then(data => {
                                        rssFeedContainer.innerHTML = data;
                                        attachPaginationListeners(); // re-attach after update :3
                                    });
                            });
                        });
                    }
                    let response = fetch("/scripts/rss2html.php?url=" + feedURL + "&fromPage=1&pageLength=<?php echo $userPreferences['itemsPerPage']?>", {
                        method: 'GET',
                    });
                    response.then(res => {
                        if (res.ok) {
                            return res.text();
                        }
                        throw new Error('Network response was not ok');
                    }).then(data => {
                        rssFeedContainer.innerHTML = data;
                        attachPaginationListeners(); // attach after first load :P
                    }).catch(error => {
                        console.error('Fetch error:', error);
                    });
                </script>
                <script>
                    
        </main>
    </body>
</html>
