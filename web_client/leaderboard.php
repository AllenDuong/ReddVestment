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
  <div class="container">
    <!-- This creates the left half (which shows global rankings) -->
    <div class="equalHWrap eqWrap">
      <div class="equalHW eq">
        <h3 style='text-align:center'>Global Rankings</h3>
        <?php
            $user = $_SESSION["username"];
            $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
            mysqli_select_db($link, 'tblazek');
            // This query gets the average scores for each user
            $query = "SELECT a.username, Players.name, a.avg FROM Players, (SELECT Investments.username, SUM(Posts.final_score)/COUNT(Posts.final_score) as avg FROM Investments, Posts WHERE Posts.id=Investments.post_id GROUP BY Investments.username ORDER BY avg desc, COUNT(Posts.final_score) desc) a WHERE Players.username=a.username";
            $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
            echo "<div class='alert alert-info'>This is the global rankings by average Investment score</div>";
            echo "<ul class='list-group'>";
            $ranking = 1;
            // This while loop displays the list group for each of the rankings. It will bold the username and name if it is equal to your username
            while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
                $query_username = $tuple['username'];
                $query_name = $tuple['name'];
                $query_avg = round($tuple['avg']);
                if ($query_username == $user) {
                    $query_username = "<b>" . $query_username . "</b>";
                    $query_name = "<b>" . $query_name . "</b>";
                }
                echo "<li class='list-group-item'><span class='badge badge-primary'>$ranking</span> $query_name ($query_username)<span class='badge badge-success' style='float:right'>$query_avg</span></li>";
                $ranking = $ranking + 1;
            }
            echo "</ul>";
            mysqli_free_result($result);
            mysqli_close($link);

      ?>
      </div>
      <div class="equalHW eq">
        <h3 style='text-align:center'>Friend Leaderboard</h3>
        <?php
            $user = $_SESSION["username"];
            $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
            mysqli_select_db($link, 'tblazek');

            // This query goes through all the game data and categorizes each game as a win, loss, or a tie. It then GROUP BY the result and the opponent to get a sum of each of the game results
            $query = "SELECT X.*, Players.name FROM (SELECT a.player1, a.player2, a.time_created, a.accepted, a.bet1, a.bet2, a.final_score1, b.final_score2, if(a.player1='$user', a.player2, a.player1) as opp, if(a.final_score1=b.final_score2, 'tie', if(a.player1='$user', if(a.final_score1>b.final_score2, 'win', 'loss'), if(b.final_score2>a.final_score1, 'win', 'loss'))) as result, count(*) as tot_games FROM (SELECT Games.*, Posts.final_score final_score1 from Games, Posts where Posts.id=bet1)a LEFT JOIN (SELECT Games.*, Posts.final_score final_score2 FROM Games, Posts where Posts.id=bet2)b ON a.player1=b.player1 AND a.player2=a.player2 AND a.time_created=b.time_created WHERE a.accepted=3 AND (a.final_score1 IS NOT NULL OR b.final_score2 IS NOT NULL) AND (a.player1='$user' OR a.player2='$user') GROUP BY opp, result ORDER BY opp) X, Players WHERE Players.username=X.opp";
            $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
            echo "<div class='alert alert-info'>This is the friend rankings sorted by username</div>";
            echo "<ul class='list-group'>";
            $curr_user = "";
            $curr_name = "";
            $curr_display = false;
            $curr_wins = 0;
            $curr_losses = 0;
            $curr_ties = 0;
            while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
                $query_opponent = $tuple['opp'];
                $query_result = $tuple['result'];
                $query_games = $tuple['tot_games'];
                
                if ($curr_user != $query_opponent){
                    // This displays the list group for the opponent once all the wins, losses, and ties are found for it
                    if ($curr_display==true){
                        if ($curr_wins > $curr_losses){
                            echo "$curr_name ($curr_user) <span class='badge badge-success' style='float:right;'>$curr_wins-$curr_losses-$curr_ties</span></li>";
                        } else if ($curr_wins < $curr_losses) {
                            echo "$curr_name ($curr_user) <span class='badge badge-danger' style='float:right;'>$curr_wins-$curr_losses-$curr_ties</span></li>";
                        } else {
                            echo "$curr_name ($curr_user) <span class='badge badge-light' style='float:right;'>$curr_wins-$curr_losses-$curr_ties</span></li>"; 
                        }
                    }
                    $curr_wins=0;
                    $curr_losses=0;
                    $curr_ties=0;
                    $curr_display=false;
                    $curr_user = $query_opponent;
                    $curr_name = $tuple['name'];
                }
                if ($query_result == "loss"){
                    $curr_losses = $query_games;
                } else if ($query_result == "win") {
                    $curr_wins = $query_games;
                } else {
                    $curr_ties = $query_games;
                }

                if ($curr_display == false) {
                    echo "<li class='list-group-item'>";
                    $curr_display = true;
                }
            }
            // This also displays the information for each friend after all the query data has been parsed.
            if ($curr_display == true){
                if ($curr_wins > $curr_losses){
                    echo "$curr_name ($curr_user) <span class='badge badge-success' style='float:right;'>$curr_wins-$curr_losses-$curr_ties</span></li>";
                } else if ($curr_wins < $curr_losses) {
                    echo "$curr_name ($curr_user) <span class='badge badge-danger' style='float:right;'>$curr_wins-$curr_losses-$curr_ties</span></li>";
                } else {
                    echo "$curr_name ($curr_user) <span class='badge badge-light' style='float:right;'>$curr_wins-$curr_losses-$curr_ties</span></li>"; 
                }
            }
            echo "</ul>";
            mysqli_free_result($result);
            mysqli_close($link);
        ?>
      </div>
    </div>
  </div>
</body>
<html>
