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

function createUserPreferences() {
    global $mrssdb;
    $query = "INSERT INTO user_preferences (id) VALUES (?)";
    $stmt = $mrssdb->prepare($query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
}

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
} else {
    header("Location: /account/login.php?from=settings");
    exit;
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
                <div class="row" id="general-selection">
                    <img src="" alt="General settings icon" class="feed-icon">
                    <h2>General</h2>
                </div>
                <div class="row" id="layout-selection">
                    <img src="/assets/images/layout.png" alt="Layout settings icon" class="feed-icon">
                    <h2>Layout</h2>
                </div>
                <div class="row" id="customisation-selection">
                    <img src="" alt="Customisation settings icon" class="feed-icon">
                    <h2>Customisation</h2>
                </div>
                <div class="row" id="account-selection">
                    <img src="" alt="Account settings icon" class="feed-icon">
                    <h2>Account</h2>
                </div>
            </div>
            <div class="rss-feed">
                <div class="settings-container" id="unselected">
                    Click a setting on the left to view it.
                </div>
                <div class="settings-container hidden" id="general">
                    <h1>General</h1>
                </div>
                <div class="settings-container hidden" id="layout">
                    <h1>Layout</h1>
                    <div class="options">
                        <div class="column-align-left">
                            <label for="itemsperpage">Items per page:</label>
                        </div>
                        <div class="column-align-right">
                            <div>
                            <span id="itemsperpage-value"><?php echo $userPreferences['itemsPerPage']; ?></span>
                            <input type="range" id="itemsperpage" name="itemsperpage" min="3" max="10" value="<?php echo $userPreferences['itemsPerPage']; ?>" class="vertical-align-middle">
                            </div>
                        </div>
                        <script>
                            const itemsPerPageValue = document.querySelector('#itemsperpage-value');
                            const itemsPerPageSlider = document.querySelector('#itemsperpage');
                            itemsPerPageSlider.addEventListener('input', () => {
                                itemsPerPageValue.textContent = itemsPerPageSlider.value;
                            });
                        </script>
                    </div>
                    <div class="settings-buttons">
                        <button id="default-layout-settings">Reset to default</button>
                        <button id="cancel-layout-settings">Undo all</button>
                        <button id="save-layout-settings">Apply</button>
                    </div>
                </div>
                <div class="settings-container hidden" id="customisation">
                    <h1>Customisation</h1>
                </div>
                <div class="settings-container hidden" id="account">
                    <h1>Account</h1>
                </div>
            </div>
            <script>
                // zzzzzzzz i'm sleepy
                const generalSelection = document.querySelector('#general-selection');
                const layoutSelection = document.querySelector('#layout-selection');
                const customisationSelection = document.querySelector('#customisation-selection');
                const accountSelection = document.querySelector('#account-selection');

                const unselectedContainer = document.querySelector('#unselected');
                const generalContainer = document.querySelector('#general');
                const layoutContainer = document.querySelector('#layout');
                const customisationContainer = document.querySelector('#customisation');
                const accountContainer = document.querySelector('#account');

                // there's probably a better way to do this but javascript
                function showContainer(container) {
                    unselectedContainer.classList.add('hidden');
                    generalContainer.classList.add('hidden');
                    layoutContainer.classList.add('hidden');
                    customisationContainer.classList.add('hidden');
                    accountContainer.classList.add('hidden');

                    container.classList.remove('hidden');
                }
                generalSelection.addEventListener('click', () => {
                    showContainer(generalContainer);
                });
                layoutSelection.addEventListener('click', () => {
                    showContainer(layoutContainer);
                });
                customisationSelection.addEventListener('click', () => {
                    showContainer(customisationContainer);
                });
                accountSelection.addEventListener('click', () => {
                    showContainer(accountContainer);
                });

            </script>
        </main>
    </body>
</html>
