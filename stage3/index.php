<?php
session_start();
?>
<!DOCTYPE html>
<html>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <head>
        <link rel="stylesheet" href="main.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="main.js"></script>
    </head>

    <body align=center onpageshow="toggleAvailableButton()">
        <header>
            <h1>ReddVestments</h1>
            <h3>Presented by RawToast</h3>
            <br>
        </header>
        
        <form>
            <h1>Login</h1>
            <input type="text" id="userName" placeholder="Enter your username." onkeyup="toggleAvailableButton()"></textarea>
            <input type="password" id="password" placeholder="Enter your password." onkeyup="toggleAvailableButton()"></textarea>
            <button class="submitButton" id="loginButton" onclick="loginButtonClick()">Submit</button>
            <br><br>
            <a href="register.html">New here? Register!</a>
        </form>

    </body>
<html>
