<?php
include 'sessionHeader.php';
define ('DBSERVER', 'localhost');
define ('DBUSER', 'tblazek');
define ('DBPASS','goirish');
define ('DBNAME','tblazek');

//LET'S INITIATE CONNECT TO DB
$connection = mysqli_connect(DBSERVER, DBUSER, DBPASS) or die("Can't connect to server. Please check credentials and try again");
$result = mysqli_select_db($connection, DBNAME) or die("Can't select database. Please check DB name and try again");

if (isset($_POST['request']) && $_POST['request'] == 'sendGameInvite') {
    //CREDENTIALS FOR DB
    $user = $_POST['user'];
    $friend_uname = $_POST['friend'];
    $subreddit = $_POST['subreddit'];
    $_SESSION["player1"] = $user;
    $_SESSION["player2"] = $friend_uname;
    $_SESSION["vsSubreddit"] = $subreddit;
    $time = time();
    $sql = "INSERT INTO Games(player1, player2, time_created, subreddit,accepted) VALUES ('$user','$friend_uname', $time, '$subreddit',1)";
    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    if ($result) {
        exit('send=success');
    } else {
        exit('send=failure');
    }
// UPDATES GAME INVITE ACCEPTED STATUS (WHETHER THEY HAVE TIMED OUT)
} else if (isset($_POST['request']) && $_POST['request'] == 'updateInvite') {
    $time=time();
    if (isset($_POST['accepted'])){
        $accepted = $_POST['accepted'];
        $friend = $_POST['friend'];
        $subreddit = $_POST['subreddit'];
        $user = $_SESSION['username'];
        $time = $time - 17;
        $sql = "UPDATE Games Set accepted=$accepted WHERE player1='$friend' AND player2='$user' AND subreddit='$subreddit' AND time_created>$time AND accepted=1";
    } else {
        $sql = "UPDATE Games SET accepted=0 WHERE time_created>($time-17) AND accepted=1";
    }
    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    if ($result) {
        exit('success');
    } else {
        exit('failure');
    }
    exit('success');
// SHOWS WHO SENT A GAME INVITE TO BE DISPLAYED
} else if (isset($_POST['request']) && $_POST['request'] == 'lookForInvite') {
    $user = $_SESSION['username'];
    $time = time();
    $sql = "SELECT player1, subreddit,time_created FROM Games WHERE player2='$user' AND accepted=1 AND time_created>($time-16)";
    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    $tuple = mysqli_fetch_array($result, MYSQL_ASSOC);
    if ($tuple){
        $player1 = $tuple['player1'];
        $_SESSION["player1"] = $player1;
        $_SESSION["player2"] = $user;
        $subreddit = $tuple['subreddit'];
        $_SESSION["vsSubreddit"] = $subreddit;
        $time = $tuple['time_created'];
        exit("$player1 $subreddit received $time");
    } else {
        $sql = "SELECT player2, subreddit, time_created FROM Games WHERE player1='$user' AND accepted=1 AND time_created>($time-16)";
        $result = mysqli_query($connection, $sql) or die ('Query Failed: ' .mysql_error());
        $tuple = mysqli_fetch_array($result, MYSQL_ASSOC);
        if ($tuple){
            $player2 = $tuple['player2'];
            $subreddit = $tuple['subreddit'];
            $time = $tuple['time_created'];
            exit("$player2 $subreddit sent $time");
        } else {
            exit("none");
        }
    }
// check for acceptance of friend request
} else if (isset($_POST['request']) && $_POST['request'] == 'lookForAccept') {
    $user = $_SESSION['username'];
    $time=time();
    $sql = "SELECT player1, player2, subreddit, time_created FROM Games WHERE (player1='$user' OR player2='$user') AND time_created>($time-20) AND accepted=2";
    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    $tuple = mysqli_fetch_array($result, MYSQL_ASSOC);
    if ($tuple){
        $_SESSION["player1"] = $tuple["player1"];
        $_SESSION["player2"] = $tuple["player2"];
        $_SESSION["vsSubreddit"] = $tuple["subreddit"];
        exit("accepted");
    } else {
        exit("failure");
    }
} else {
    exit('No request');
}
?>
