<?php
// handle logout, remove session variable and redirect user to login screen
session_start();

include_once __DIR__ . '/OnlineFriends.php';

$app = new OnlineFriends();
$app->logout($_SESSION['username']);

session_destroy();
$_COOKIE = array();
$_SESSION = array();

header('Location: ../index.php');
?>
