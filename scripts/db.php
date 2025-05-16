<?php
$params = json_decode(file_get_contents(__DIR__ . '/../params.json'));
$dbpasswordlocation = $params->dbpasswordlocation;
// two databases because MateisHomePage stores user accounts in it's own database, not the mRSS database
$userdb = $params->userdbname;
$maindb = $params->maindbname;
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = file_get_contents($dbpasswordlocation);
$db = 'forum';

$userdb = new mysqli($dbhost, $dbuser, $dbpass, $userdb);
if ($userdb->connect_errno) {
    die("Could not connect to the user account database: " . $userdb->connecterrno);
}

$mrssdb = new mysqli($dbhost, $dbuser, $dbpass, $maindb);
if ($mrssdb->connect_errno) {
    die("Could not connect to the mRSS database: " . $mrssdb->connecterrno);
}

return [
    'userdb' => $userdb,
    'mrssdb'  => $mrssdb
];