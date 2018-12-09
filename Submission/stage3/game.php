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
        <form style="width:75%">
            <h1 style='text-align:center; font-size:30px'>Select a Subreddit to Predict</h1>
            <div class="alert alert-info" style="text-align:center; font-size:15px;">Note: you cannot have more than one active investment per subreddit. After you invest, you will need to wait 12 hours until you can invest in that subreddit again.</div>
            <select name="subselect" id="subselect" class="custom-select" onchange="updateURLsubreddit(this.value)">
                <option value=''></option>
                <?php // This php connects to the database and outputs which subreddits can be invested
                    $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
                    mysqli_select_db($link, 'tblazek');
                    $user = $_SESSION["username"];
                    // This query will give all the subreddits that the user can invest in. Only can invest in a subreddit after 12 hours of their last investment.
		            $query = "SELECT DISTINCT Posts.subreddit FROM Posts LEFT JOIN ((SELECT subreddit, MAX(time_chosen) as max FROM Investments WHERE username='$user' GROUP BY subreddit HAVING max > (UNIX_TIMESTAMP() - (12*60*60))) UNION (SELECT subreddit, max(time_created) as max FROM Games WHERE (player1='$user' OR player2='$user') GROUP BY subreddit HAVING max > (UNIX_TIMESTAMP() - (12*60*60)))) u ON u.subreddit=Posts.subreddit WHERE u.subreddit IS NULL";
                $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
                $_SESSION["curr_time"] = time();
                
                // This while loop adds to the options of which subreddit the user can invest in
                while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
                    $subreddit = $tuple["subreddit"];
                    echo "<option value='$subreddit'>$subreddit</option>";
                }
                mysqli_free_result($result);
                mysqli_close($link);
            ?>
            </select>
        </form>
        <!-- Displays the game information -->
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
                <button class="changeButton" id="choiceButton" onclick="readChoiceClick('invest')">Submit Guess</button>
            </div>
        </form>
    </body>
    <br><br><br>
    <html>
