<?php include 'php/sessionHeader.php';?>
<!DOCTYPE html>
<html>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<head>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="main.js"></script>
</head>

<body>
    <?php include 'php/header.php';?>

    <body align=center>
        <!-- Need to fill out the game information -->
        <form id="game_info" style="display: none; width: 75%;">
            <h1 style='text-align:center; font-size:25px;'>Select one of the following posts that will do the best</h1>
            <div id='subreddit_load'>
            </div>
            <?php
            if (isset($_SESSION["sub"])) {
                if ($_SESSION["sub"] != ""){
                    echo $_SESSION["sub"];
                }
            }
        ?>
            <br>
            <div class="text-center">
                <button class="changeButton" id="choiceButton" onclick="readChoiceClickVs()">Submit Guess</button>
            </div>
        </form>
    </body>
    <script type="text/javascript"> updateURLsubreddit('<?php if(isset($_SESSION['vsSubreddit'])) { echo $_SESSION['vsSubreddit']; } else { window.location.replace("friends.php"); } ?> '); </script>
    <br><br><br>
<html>
