<?php
include 'sessionHeader.php';
define ('DBSERVER', 'localhost');
define ('DBUSER', 'tblazek');
define ('DBPASS','goirish');
define ('DBNAME','tblazek');

//LET'S INITIATE CONNECT TO DB
$connection = mysqli_connect(DBSERVER, DBUSER, DBPASS) or die("Can't connect to server. Please check credentials and try again");
$result = mysqli_select_db($connection, DBNAME) or die("Can't select database. Please check DB name and try again");

$user = $_SESSION['username'];

if (isset($_POST['request']) && $_POST['request'] == 'sendInvitation') {
    //CREDENTIALS FOR DB
    $friend_uname = $_POST['friend_uname'];
    $sql = "SELECT username, name FROM Players LEFT JOIN (SELECT uname FROM ((SELECT receiver as uname FROM Invitations WHERE inviter='$user' AND accepted!='no') UNION (SELECT inviter as uname FROM Invitations WHERE receiver='$user' AND accepted!='no'))u) u2 ON Players.username=u2.uname WHERE u2.uname IS NULL AND username!='$user' AND username='$friend_uname'";
    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    if ($result->num_rows == 0) {
        exit('search=failure');
    }
    // If valid username AND that person hasn't received a friend request or said no
    $sql = "SELECT receiver, inviter FROM Invitations WHERE (inviter='$user' AND receiver='$friend_uname') OR (receiver='$user' AND inviter='$friend_uname')";
    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    // Need to check whether the requested person has in the past declined the invitation or 
    if ($result->num_rows == 0) { // An invitation has never existed between the two people
        $sql = "INSERT INTO Invitations VALUES('$user', '$friend_uname', UNIX_TIMESTAMP( NOW()), NULL)";
    } else { // the invitation has existed in the past, but was declined
        $sql = "UPDATE Invitations SET accepted=NULL, inviter='$user', receiver='$friend_uname' WHERE (inviter='$friend_uname' AND receiver='$user') OR (receiver='$friend_uname' AND inviter='$user')";
    }

    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    exit('search=success');
} else if (isset($_POST['request']) && $_POST['request'] == 'requestResponse') {
    $inviter = $_POST['inviter'];
    $receiver = $_POST['receiver'];
    $accepted = $_POST['accepted'];
    $sql = "SELECT receiver, inviter FROM Invitations WHERE inviter='$inviter' and receiver='$receiver'";
    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' .mysql_error());
    if ($result->num_rows > 0) {
        $sql = "UPDATE Invitations SET accepted='$accepted' WHERE inviter='$inviter' AND receiver='$receiver'";
    } else {
        $sql = "UPDATE Invitations SET accepted='$accepted' WHERE inviter='$receiver' AND receiver='$inviter'";
        
    }

    $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
    exit('success');
} else {
    exit('No request');
}
?>
