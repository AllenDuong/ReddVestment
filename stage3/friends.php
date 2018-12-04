<!-- Load header -->
<?php include 'php/sessionHeader.php';?>
<!DOCTYPE html>
<html>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="main.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    
</head>
<?php include 'php/header.php';?>

<body>

<div class="modal fade" id="gameInviteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Game Invite</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="game-invite-body"></div>
        <div class="progress">
            <div class="progress-bar" id="progress-bar-modal" role="progressbar"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="gameInviteDecline" class="btn btn-danger" data-dismiss="modal">Decline</button>
        <button type="button" id="gameInviteAccept" class="btn btn-success">Accept</button>
      </div>
    </div>
  </div>
</div>


<!-- Friend Box and Friend Request Box -->
  <div class="container">
    <div class="equalHWrap eqWrap">
      <div class="equalHW eq">
        <h3 style='text-align:center'>Online Friends</h3>
        <ul class="list-group" id="online_friends">
<?php
/**
                if(!empty($online_friends)) {
                    foreach ($online_friends as $friend) {
                        echo '<li class="list-group-item">' . '<span style="color:rgb(66,183,42);">• </span>' . $friend['name'] . " (" . $friend['username'] . ")\t" .'</li>';
                    }
                } else {
                    echo '<li class="list-group-item">No friends online.</li>';
                }
                **/
            ?>
        </ul>
      </div>

      <div class="equalHW eq">
        <!-- Search for friend -->
        <h3 style='text-align:center'>Friend Requests</h3>
        <input id="friends" type="text" name="friends" placeholder="Search for new friends by username" onkeydown="searchForNewFriend()">
            <?php
                $user = $_SESSION['username'];
                $connection = mysqli_connect('localhost', 'tblazek', 'goirish', 'tblazek') or die ('Database Connection Error');
                $db_connection = mysqli_select_db($connection, 'tblazek') or die("Can't select database. Please check DB name and try again");

                $sql = "SELECT inviter, Players.name as name, Players.photo as photo FROM Invitations, Players WHERE receiver='$user' AND accepted IS NULL AND Players.username=Invitations.inviter";
                $result = mysqli_query($connection, $sql) or die ('Query Failed: ' . mysql_error());
                echo "<div class='card-deck'>";
                while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
                    $image = $tuple['photo'];
                    $name = $tuple['name'];
                    $inviter = $tuple['inviter'];
                    echo "<div class='card bg-light border-dark' style='max-width: 25%'>";
                    echo "<img class='card-img-top' src='$image' style='max-width: 100%; max-height:150px;' >";
                    echo "<div class='card-body' style='padding: 0.8rem;'><h5 class='card-title'>$name</h5>";
                    echo "<button onClick=\"requestResponse('".$inviter."', '".$user."', 'yes')\" class='btn btn-success' style='display: inline'>Accept</button> ";
                    echo "<button onClick=\"requestResponse('".$inviter."', '".$user."', 'no')\" class='btn btn-danger'>Decline</button>";
                    echo "</div></div>";
                }
                echo "</div>";
                mysqli_free_result($result);
                mysqli_close($connection);
                
            ?>
        </div>
    </div>
  </div>

</body>
</html>
