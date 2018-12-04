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

    <div class="container" style="max-width:50%;">
  <!-- SQL Queries -->
        <?php
            $user = $_SESSION["username"];
            $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
            mysqli_select_db($link, 'tblazek');

            $query = "SELECT a.username, Players.name, a.avg FROM Players, (SELECT Investments.username, SUM(Posts.final_score)/COUNT(Posts.final_score) as avg FROM Investments, Posts WHERE Posts.id=Investments.post_id GROUP BY Investments.username ORDER BY avg desc, COUNT(Posts.final_score) desc) a WHERE Players.username=a.username";
            $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
            echo "<div class='alert alert-info'>This is the global rankings by average Investment score</div>";
            echo "<ul class='list-group'>";
            $ranking = 1;
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
</body>
<html>
