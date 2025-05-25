<?php
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
        createHeadSection('mRSS - Login'); ?>
    </head>
    <body>
        <header>
            <?php require_once __DIR__ . '/../segments/header.php'; ?>
        </header>
        <main>
            <div class="login-container">
                <h1>Login</h1>
                <form action="/account/login.php" method="post">
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username" required><br>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br>
                    <button type="submit">Login</button>
                </form>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $sql = "SELECT * FROM users WHERE username = ?";
                    $stmt = $userdb->prepare($sql);
                    if ($stmt === false) {
                        die("<i>Database error: " . $userdb->error . '</i>');
                    }
                    $stmt->bind_param('s', $_POST['username']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    if ($user && password_verify($_POST['password'], $user['hashed_password'])) {
                        session_regenerate_id();
                        $_SESSION['user_id'] = $user['id'];
                        // Log the user IP address
                        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
                        if ($user['first_ip']) {
                            $logSql = 'UPDATE users SET last_ip = ? WHERE username = ?';
                        } else {
                            $logSql = 'UPDATE users SET first_ip = ?, last_ip = ? WHERE username = ?';
                        }
                        $logStmt = $userdb->prepare($logSql);
                        if ($logStmt === false) {
                            die("<i>Database error: " . $userdb->error . '</i>');
                        }
                        if ($user['first_ip']) {
                            $logStmt->bind_param('ss', $ip, $_POST['username']);
                        } else {
                            $logStmt->bind_param('sss', $ip, $ip, $_POST['username']);
                        }
                        $logStmt->execute();
                        echo '<script>window.location.href = "/";</script>';
                        exit;
                    } else {
                        echo '<i>Invalid login credentials</i>';
                    }
                }
                ?>
                <p>Don't have an account? <a href="/account/register.php">Register here</a>.</p>
            </div>
        </main>
    </body>
</html>
