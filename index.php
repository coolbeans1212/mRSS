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
                    let feedURL = encodeURIComponent("https://amigaos.net/taxonomy/term/7/feed");
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

                    // so this is the bit where you click on 'new feed' and it appears and it also does the thing
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
                                    <input type="text" id="feed-url" name="feed-url" required> <button type="button" id="fetch-feed-info">Continue</button>
                                </div>
                                <div class="add-feed-buttons">
                                    <button type="submit" disabled>Add Feed</button>
                                </div>
                            </form>
                        `;
                        floatingWindowBackdrop.appendChild(floatingWindow);
                        let feedForm = document.querySelector('#new-feed-form');
                        let feedFormMainBody = feedForm.querySelector('.rss-item-description');
                        let feedUrlInput = document.querySelector('#feed-url');
                        let fetchFeedInfoButton = document.querySelector('#fetch-feed-info');
                        fetchFeedInfoButton.addEventListener('click', () => {
                            fetchFeedInfoButton.disabled = true;
                            let feedUrl = feedUrlInput.value.trim();
                            if (feedUrl === '') {
                                alert('Please enter a valid feed URL.');
                                fetchFeedInfoButton.disabled = false;
                                return;
                            }
                            fetch('/api/getFeedInfo.php?feedUrl=' + encodeURIComponent(feedUrl))
                                .then(response => response.json())
                                .then(feedInfo => {
                                    if (feedInfo.error) {
                                        if (document.querySelector('.error-display')) {
                                            document.querySelector('.error-display').remove();
                                        }
                                        let errorDisplay = document.createElement('div');
                                        errorDisplay.className = 'error-display';
                                        errorDisplay.textContent = feedInfo.error;
                                        feedFormMainBody.appendChild(errorDisplay);
                                        fetchFeedInfoButton.disabled = false;
                                        return;
                                    } else {
                                        if (document.querySelector('.error-display')) {
                                            document.querySelector('.error-display').remove();
                                        }
                                        let title = feedInfo.title || 'No Title';
                                        let description = feedInfo.description || 'No Description';
                                        let image = feedInfo.image || '';
                                        floatingWindow.innerHTML += `
                                            <div class="rss-item-description">
                                            <div class="form-row">
                                                <label for="feed-title">Feed Title:</label>
                                                <input type="text" id="feed-title" name="feed-title" value="${title}" required><br>
                                            </div>
                                            <div class="form-row">
                                                <label for="feed-description">Feed Description:</label>
                                                <textarea id="feed-description" name="feed-description">${description}</textarea><br>
                                            </div>
                                            <div class="form-row">
                                                <label for="feed-image">Feed Image URL:</label>
                                                <input type="text" id="feed-image" name="feed-image" value="${image}"> <img src="${image}" class="feed-image"><br>
                                            </div>
                                            <div class="add-feed-buttons">
                                                <button type="submit">Add Feed</button>
                                            </div>
                                        `;
                                        document.querySelector('#feed-url').value = `${feedUrl}`;
                                    }
                                })
                            });
                            


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
