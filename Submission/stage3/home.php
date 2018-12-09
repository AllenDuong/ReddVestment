<!-- Load header -->
<?php include 'php/sessionHeader.php';?>
<!DOCTYPE html>
<html>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<head>
  <link rel="stylesheet" href="home.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="main.js"></script>
</head>

<body>
  <?php include 'php/header.php';?>

  <!-- SQL Queries -->
  <?php
  $user = $_SESSION["username"];
  $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
  mysqli_select_db($link, 'tblazek');

  // This query gets the user information from Players
  $query = "select name, photo from Players where username = '$user'";

  $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());

  while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $name = $tuple['name'];
    $photo = $tuple['photo'];
  }
  // This query gets the total points and total games from Investments and from the Vs game.
  $querySumCount = "SELECT SUM(final_score) as total_points, count(*) as total_games FROM ((SELECT Posts.final_score, Games.time_created as time_chosen FROM Games, Posts WHERE accepted=3 AND (player1='$user' AND Games.bet1=Posts.id) OR (player2='$user' AND Games.bet2=Posts.id)) UNION (SELECT Posts.final_score, Investments.time_chosen FROM Investments, Posts WHERE Investments.username='$user' AND Investments.post_id=Posts.id)) u ORDER BY time_chosen desc";
 
  $querySumCountResult = mysqli_query($link, $querySumCount) or die ("Query failed: " . mysql_error());
  $tuple = mysqli_fetch_array($querySumCountResult, MYSQL_ASSOC);
  $tpoints = $tuple['total_points'];
  if($tpoints == null){
    $tpoints = 0;
  }
  $tgames = $tuple['total_games'];
  if($tgames == null){
    $tgames = 0;
  }
  mysqli_free_result($result);
  mysqli_close($link);
  ?>

  <!-- Profile Box and Current Investments -->
  <div class="container">
    <ul class="list-group">
      <li class="list-group-item" style="background-color: #DADCDF; border:solid;">
        <img src="<?=$photo?>" alt="<?=$photo?>" style="float: left; width:150px; height:150px;border-style: solid; border-width: 1px;">

        <p style="padding-left: 165px;">
          <!-- Displays information from the queries above -->
          <b>Name:</b>
          <?=$name?><br>
          <b>Username:</b>
          <?=$user?><br>
          <b>Lifetime Karma:</b>
          <?=$tpoints?><br>
          <b>Total Posts:</b>
          <?=$tgames?><br>
        </p>
      </li>
      <li class="list-group-item" style="border:solid;">
        <!-- Pull all from Investments and from VS Mode -->
        <?php
      $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
      mysqli_select_db($link, 'tblazek');
        // This gets the data from Games and Vs.
        $query = "SELECT * FROM ((SELECT Games.subreddit as subreddit, Posts.title as title, from_unixtime(Games.time_created) as time_chosen, Posts.final_score, Posts.thumbnail, Posts.author, Posts.id, Games.player1, Games.player2 FROM Games, Posts WHERE accepted=3 AND (player1='$user' AND bet1=Posts.id) OR (player2='$user' AND bet2=Posts.id)) UNION (SELECT Investments.subreddit as subreddit, Posts.title as title, from_unixtime(Investments.time_chosen) as time_chosen, Posts.final_score, Posts.thumbnail, Posts.author, Posts.id, Investments.username, '' FROM Investments, Posts WHERE Investments.username='$user' AND Investments.post_id=Posts.id)) u ORDER BY time_chosen desc";
      $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
      ?>
      <div class="alert alert-secondary text-center">
      If your scores don't show up on the left side, wait 12 hours for the posts to update. Blue background indicates matches against friends.
      </div>
      <?php
      echo '<style>    img{
        width: 100px;
        height: 100px;
      }
      </style>
          <div class="container">
      <div class="list-group" id="redditTable">';
      while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
          // The formatting looks bad, but basically it creates a table that shows the score, reddit image, and reddit description for each post. It also creates a linkthat goes to the actual reddit post
          echo '<a target="_blank" href="http://reddit.com/'. $tuple["id"] .'" type="button"'; 
          if ($tuple["player2"] == ""){
              echo 'class="list-group-item list-group-item-action">';
          } else{
              echo 'class="list-group-item list-group-item-action list-group-item-info">';
          }
        echo '<table class="table table-sm" style="height: 100px; margin-bottom: 0;">
        <tbody style="border-top: none;">
        <tr>
        <td class="align-middle" style="border-top: none; width: 15%">
                      <div class="text-center">
                      <h2>';
            echo '<span class="badge badge-warning">'. $tuple["final_score"] .'</span>';
            echo '           </h2>
                      </div>
                    </td>
          <td class="align-middle" style="width: 16.66%; border-top: none;">';
        if(strpos($tuple["thumbnail"], "http") === 0){
            echo '<img src="'. addslashes($tuple["thumbnail"]) .'" onerror=this.src="https://b.thumbs.redditmedia.com/yV6o42cnJzuzWU03wj_eCjJE1Y8OnlYslVW2OYT7oFQ.jpg" class="rounded float-left">';
        }else{
            echo '<img src="https://b.thumbs.redditmedia.com/yV6o42cnJzuzWU03wj_eCjJE1Y8OnlYslVW2OYT7oFQ.jpg" class="rounded float-left">';
        }
        echo '</td>
        <td class="align-middle" style="text-align: left; border-top: none;">
          <ul class="list-group list-group-flush">
            <li class="list-group-item bg-transparent">'. $tuple["title"] .'</li>
            <li class="list-group-item bg-transparent"><strong>r/'. $tuple["subreddit"] .'</strong> by u/'. $tuple["author"] .', picked at '. $tuple["time_chosen"] .'.
          </ul>
        </td>
      </tr>
    </tbody>
  </table>
</a>';
      }
      mysqli_free_result($result);
      mysqli_close($link);
      ?>
      </li>
    </ul>
  </div>
  <br>
</body>
<html>
