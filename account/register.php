<?php
require_once __DIR__ . '/../scripts/functions.php';
$dbs = require __DIR__ . '/../scripts/db.php';
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
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once __DIR__ . '/../scripts/createHeadSection.php';
        createHeadSection('mRSS - Register'); ?>
    </head>
    <body>
        <header>
            <?php require_once __DIR__ . '/../segments/header.php'; ?>
        </header>
        <main>
            <div class="login-container">
                <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ?>
                <h1>Register</h1>
                <form action="/account/register.php" method="post">
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username" required><br>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br>
                    <label for="confirm_password">Confirm Password:</label><br>
                    <input type="password" id="confirm_password" name="confirm_password" required><br>
                    <label for="email">Email:</label><br>
                    <input type="email" id="email" name="email" required><br>
                    <button type="submit">Register</button>
                </form>
                <span>Protected by <img src="/assets/images/stopforumspam.png" alt="stopforumspam" height="20px" class="vertical-align-middle">.</span>
                <?php } else {
                    // Check if the request is rate limited
                    $ipAddress = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
                    if (!rateLimit($userdb, $ipAddress, 10, 60)) {
                        die('Rate limit exceeded. Please wait 60 seconds. <b><a href="">Click here to try again</a></b>');
                    }
                    if ($_POST['password'] !== $_POST['confirm_password']) {
                        die('Passwords do not match. <b><a href="">Click here to try again</a></b>');
                    }
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $email = $_POST['email'];
                    // Check if username is valid (alphanumeric, >25 characters)
                    if (strlen($username) >= 25 || ctype_alnum($username) === false) {
                        die('Username must be 25 characters or less and only contain characters A-z 0-9. <b><a href="">Click here to try again</a></b>');
                    }
                    // Check if username is already taken
                    $sql = "SELECT * FROM users WHERE username = ?";
                    $stmt = $userdb->prepare($sql);
                    if ($stmt === false) {
                        die("Database error: " . $userdb->error . '. <b><a href="">Click here to try again</a></b>');
                    }
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $result = $result->fetch_assoc();
                    if ($result) {
                        die('Username is already taken. <b><a href="">Click here to try again</a></b>');
                    }
                    // Check if password is valid (at least 8 characters, not too common)
                    if (strlen($password) < 8) {
                        die('Password must be at least 8 characters long. <b><a href="">Click here to try again</a></b>');
                    }
                    $passwordsToDenyRaw = file_get_contents(__DIR__ . '/../assets/json/PwnedPasswordsTop100k.json');            
                    $passwordsToDeny = json_decode($passwordsToDenyRaw, true);
                    if (binarySearchArray($passwordsToDeny, $password) !== -1) { // binary search is so fast what // binary search is so fast <i>why</i>?
                        die('Password is too weak / common. <b><a href="">Click here to try again</a></b>');
                    }
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    // Check if e-mail is valid, then check if it is in the stopforumspam database
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        die('Invalid email address. <b><a href="">Click here to try again</a></b>');
                    }
                    $stopForumSpamResponse = file_get_contents('https://api.stopforumspam.org/api?email=' . urlencode($email) . '&f=json');
                    $stopForumSpamResponse = json_decode($stopForumSpamResponse, true);
                    if (isset($stopForumSpamResponse['email']['appears']) && $stopForumSpamResponse['email']['appears']) {
                        die('nuh uh. <b><a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">You don\'t get to try again.</a></b>'); //security through obscurity am i right fellas
                    }
                    // Check if Internet Protocol address is in the stopforumspam database
                    $stopForumSpamResponse = file_get_contents('https://api.stopforumspam.org/api?ip=' . urlencode($ipAddress) . '&f=json');
                    $stopForumSpamResponse = json_decode($stopForumSpamResponse, true);
                    if (isset($stopForumSpamResponse['ip']['appears']) && $stopForumSpamResponse['ip']['appears']) {
                        die('Potential spammer blocked. <b><a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">You ESPECIALLY don\'t get to try again.</a></b>');
                    }

                    // yippee!!! all the checks passed :D ok time to become a real account!!!!
                    $sql = "INSERT INTO users (username, hashed_password, email, pfp) VALUES (?, ?, ?, ?)";
                    $stmt = $userdb->prepare($sql);
                    if ($stmt === false) {
                        die("Database error: " . $userdb->error . '. <b><a href="">Click here to try again</a></b>');
                    }
                    $stmt->bind_param('sssi', $username, $hashedPassword, $email, mt_rand(1, 78));
                    if ($stmt->execute()) {
                        die('Account created successfully! <b><a href="/account/login.php">Click here to log in</a></b>');
                    } else {
                        die("Database error: " . $stmt->error . '. <b><a href="">Click here to try again</a></b>');
                    }
                }
                ?>
            </div>
        </main>
    </body>
</html>
