<?php
if(!session_id())session_start();
$sessVal=isset($_SESSION['user']) ? $_SESSION['user'] : null;

echo $sessVal;
?>