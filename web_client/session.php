<!-- This is a header file for all of the pages. It starts the session and makes sure that they are logged in -->
<?php
    if(!session_id())session_start();
    $sessVal=isset($_SESSION['user']) ? $_SESSION['user'] : null;

    echo $sessVal;
?>
