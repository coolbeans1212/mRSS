<?php
ini_set('session.cookie_domain', '.mateishome.page');
session_start();
session_destroy();
header('Location: /');
