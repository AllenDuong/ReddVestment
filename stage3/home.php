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

  $query = "select name, total_points, total_games, photo from Players where username = '$user'";
  $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());

  while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $name = $tuple['name'];
    $photo = $tuple['photo'];
  }
  $querySumCount = "select SUM(Posts.final_score) as total_points, count(*) as total_games from Investments, Posts where username='". $user ."' and Posts.id=Investments.post_id";
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
        <!-- Pull Current Investments -->
        <?php
      $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
      mysqli_select_db($link, 'tblazek');

      $query = "SELECT Investments.subreddit as subreddit, Posts.title as title, from_unixtime(Investments.time_chosen) as time_chosen, Posts.final_score, Posts.thumbnail, Posts.author, Posts.id FROM Investments, Posts WHERE Investments.username='$user' AND Investments.post_id=Posts.id";
      $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
      ?>
      <div class="alert alert-secondary text-center">
      If your scores don't show up on the left side, wait 12 hours for the posts to update.
      </div>
      <?php
      echo '
<style>
img{
    width: 100px;
    height: 100px;
}
</style>
      <div class="container">
      <div class="list-group" id="redditTable">';
      while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        echo '<a target="_blank" href="http://reddit.com/'. $tuple["id"] .'" type="button" class="list-group-item list-group-item-action">
        <table class="table table-sm" style="height: 100px; margin-bottom: 0;">
        <tbody style="border-top: none;">
        <tr>
        <td class="align-middle" style="border-top: none; width: 15%">
                      <div class="text-center">
                        <h2>
                        <span class="badge badge-warning">'. $tuple["final_score"] .'</span>
                        </h2>
                      </div>
                    </td>
          <td class="align-middle" style="width: 16.66%; border-top: none;">';
        if(strpos($tuple["thumbnail"], "http") === 0){
          echo '<img src="'. addslashes($tuple["thumbnail"]) .'" class="rounded float-left">';
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
