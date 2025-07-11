<?php
$dbs = require __DIR__ . '/scripts/db.php';
require_once __DIR__ . '/scripts/functions.php';
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
    // validate timezone (because alex felt like having a timezone of 'One' and still wanted the site to work)
    $timezones = DateTimeZone::listIdentifiers();
    $timezoneExists = false;
    foreach ($timezones as $timezone) {
        if ($timezone === $userPreferences['timezone']) {
            $timezoneExists = true;
            break;
        }
    }
    if ($timezoneExists !== true) {
        $userPreferences['timezone'] = 'Europe/London';
    }
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
                <div class="add-new-container">
                    <div class="add-new" id="new-feed">New Feed</div>
                    <div class="add-new" id="new-category">New Category</div>
                </div>
            </div>
            <div class="rss-feed">
            </div>
                <script>
                    let rssFeedContainer = document.querySelector('.rss-feed');
                    let feedURL = encodeURIComponent("https://feeds.bbci.co.uk/news/world/rss.xml");
                    let urlParams = '?url=' + feedURL + '&pageLength=<?php echo $userPreferences['itemsPerPage']?>&timezone=<?php echo $userPreferences['timezone']?>';
                    function attachListeners() {
                        document.querySelectorAll('.different-page').forEach(page => {
                            page.addEventListener('click', () => {
                                let url = new URL(window.location.href);
                                url.searchParams.set('fromPage', page.id);
                                // Instead of reloading, fetch new page content!
                                fetch("/scripts/rss2html.php" + urlParams + "&fromPage=" + page.id)
                                    .then(res => res.text())
                                    .then(data => {
                                        rssFeedContainer.innerHTML = data;
                                        attachListeners(); // re-attach after update :3
                                    });
                            });
                        });

                        document.querySelectorAll('.rss-item').forEach(item => {
                            item.addEventListener('click', () => {
                                let floatingWindowBackdrop = document.createElement('div');
                                floatingWindowBackdrop.className = 'floating-rss-item-backdrop';
                                document.querySelector('.rss-feed').appendChild(floatingWindowBackdrop);

                                let floatingWindow = document.createElement('div');
                                floatingWindow.className = 'floating-rss-item boing-boing';
                                floatingWindow.id = 'floating-window';
                                floatingWindow.innerHTML = item.innerHTML;
                                floatingWindow.style.margin = document.querySelector('.rss-feed').offsetHeight + 'px';
                                floatingWindowBackdrop.appendChild(floatingWindow);

                                setTimeout(() => {
                                    floatingWindow.style.margin = '5vh 5vw';
                                }, 10);

                                // antés... clicking on floatingWindow would also trigger the backdrop click event... pero ahora... no :D
                                floatingWindow.addEventListener('click', (e) => {
                                    e.stopPropagation();
                                });
                                floatingWindowBackdrop.addEventListener('click', () => {
                                    closeFloatingWindow();
                                });
                                document.addEventListener('keydown', (e) => {
                                    if (e.key === 'Escape') {
                                        closeFloatingWindow();
                                    }
                                });

                            });
                            });
                    }

                    function closeFloatingWindow() {
                        let floatingWindowBackdrop = document.querySelector('.floating-rss-item-backdrop');
                        let floatingWindow = document.querySelector('#floating-window');
                        floatingWindowBackdrop.remove();
                        floatingWindow.remove();
                    }
                    
                    let response = fetch("/scripts/rss2html.php" + urlParams + "&fromPage=1", {
                        method: 'GET',
                    });
                    response.then(res => {
                        if (res.ok) {
                            return res.text();
                        }
                        throw new Error('Network response was not ok');
                    }).then(data => {
                        rssFeedContainer.innerHTML = data;
                        attachListeners(); // attach after first load :P
                    }).catch(error => {
                        console.error('Fetch error:', error);
                    });

                    // so this is the bit where you click on 'new feed' and it appears
                    document.querySelector('#new-feed').addEventListener('click', () => {
                        let floatingWindowBackdrop = document.createElement('div');
                        floatingWindowBackdrop.className = 'floating-rss-item-backdrop';
                        document.querySelector('.rss-feed').appendChild(floatingWindowBackdrop);
                        let floatingWindow = document.createElement('div');
                        floatingWindow.className = 'floating-small-window boing-boing';
                        floatingWindow.style.margin = document.querySelector('.rss-feed').offsetHeight + 'px';
                        floatingWindow.id = 'floating-window';
                        floatingWindow.innerHTML = `
                            <h1>Add New Feed</h1>
                            <form id="new-feed-form">
                                <div class="rss-item-description">
                                    <label for="feed-url">Feed URL:</label>
                                    <input type="text" id="feed-url" name="feed-url" required>
                                </div>
                                <div class="add-feed-buttons">
                                    <button type="submit">Add Feed</button>
                                </div>
                            </form>
                        `;
                        floatingWindowBackdrop.appendChild(floatingWindow);
                        setTimeout(() => {
                            floatingWindow.style.margin = '5vh 5vw';
                        }, 10);
                        floatingWindow.addEventListener('click', (e) => {
                            e.stopPropagation();
                        });
                        floatingWindowBackdrop.addEventListener('click', () => {
                            closeFloatingWindow();
                        });
                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape') {
                                closeFloatingWindow();
                            }
                        });
                    });
                </script>
        </main>
    </body>
</html>
