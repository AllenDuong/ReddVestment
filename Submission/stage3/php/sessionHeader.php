<?php

// This file starts the session for all the pages and makes sure that each of the user had cookies
session_start();
if (isset($_SESSION["username"]) && isset($_COOKIE["username"]) && $_SESSION["username"] == $_COOKIE["username"] && isset($_SESSION["is_loggedin"]) && $_SESSION["is_loggedin"] == true) {
    include_once __DIR__ . '/OnlineFriends.php';

    $app = new OnlineFriends();
    $online_friends = $app->getOnlineFriends($_SESSION["username"]);
} else {
    echo "<script type='text/javascript'>
        alert('Please Login.');
        window.location.replace('index.php')
    </script>";
}
?>
