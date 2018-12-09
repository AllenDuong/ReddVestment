<?php
session_start();
$servername = "localhost";
$username = "tblazek";
$password = "goirish";
$dbname = "tblazek";

$uname = $_SESSION['username'];

if(isset($_GET["subreddit"])){
    $subreddit = $_GET["subreddit"];
    if ($subreddit == "") {
        exit('auth=success');
    }
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    // create a set of five posts that allow the users to click on one and submit their choice
    $sql = "SELECT title, id, thumbnail, author, predicted_score FROM Posts WHERE subreddit='$subreddit' ORDER BY created desc LIMIT 5";
    $result = $conn->query($sql) or die (exit('Query failed'));
    $sub = "<script src='gameChooser.js'></script>
<style>
img{
    width: 100px;
    height: 100px;
}
</style>";

    $sub = $sub . '<div class="container">
    <div class="list-group" id="redditTable">';

    $highest_predicted_score_query = "SELECT predicted_score FROM (SELECT predicted_score FROM Posts WHERE subreddit='" . $subreddit . "' ORDER BY created desc LIMIT 5) a ORDER BY predicted_score desc LIMIT 1";
    $highest_predicted_score_query_result = $conn->query($highest_predicted_score_query) or die (exit("Query Failed"));
    $highest_predicted_score_query_result_assoc = $highest_predicted_score_query_result->fetch_assoc();
    $highest_predicted_score = $highest_predicted_score_query_result_assoc["predicted_score"];
    while($row = $result->fetch_assoc()){
        $sub = $sub . '<button name="buttonSelected" type="button" value="'. $row["id"] .'" class="list-group-item list-group-item-action">
        <table class="table table-sm" style="height: 100px; margin-bottom: 0;">
        <tbody style="border-top: none;">
            <tr>
            <td class="align-middle" style="width: 16.66%; border-top: none;">';
            // default thumbnail if one doesn't exist for the post
            if(strpos($row["thumbnail"], "http") === 0){
            $sub = $sub . '<img src="'. addslashes($row["thumbnail"]) .'" class="rounded float-left">';
        } else{
            $sub = $sub . '<img src="https://b.thumbs.redditmedia.com/yV6o42cnJzuzWU03wj_eCjJE1Y8OnlYslVW2OYT7oFQ.jpg" class="rounded float-left">';
        }
        $sub = $sub . '<td class="align-middle" style="text-align: left; border-top: none;">
        <ul class="list-group list-group-flush">
            <li class="list-group-item bg-transparent">';
            // display the "Predicted" badge when the post has the highest predicted score
            if($highest_predicted_score == $row["predicted_score"]){
                $sub = $sub . '<span class="badge badge-primary">Predicted</span> ';
            }
            $sub = $sub . $row["title"] .'</li>
            <li class="list-group-item bg-transparent">By: u/'. $row["author"] .'</li>
        </ul>
        </td>
    </tr>
    </tbody>
</table>
</button>';
    }

    $sub = $sub . '    </div>
    </div>';
    $conn->close();
    if($result->num_rows != 5){
        $sub = "";
    }
    echo $sub;
}

if(isset($_POST['request']) && $_POST['request'] == 'chooseRedPost') {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $mode = $_POST["mode"];
    // handle when the user sends a vs mode request
    if ($mode == "vs") {
        $player1 = $_SESSION["player1"];
        $player2 = $_SESSION["player2"];
        $subreddit = $_SESSION["vsSubreddit"];
        $redpost_id = $_POST["redpost_id"];
        if ($player1 == $uname) {
            $sql = "UPDATE Games SET bet1='$redpost_id', accepted=3 WHERE player1='$player1' AND player2='$player2' AND subreddit='$subreddit' AND time_created=(SELECT max FROM (SELECT MAX(time_created) max FROM Games WHERE player1='$player1' AND player2='$player2' AND subreddit='$subreddit' and (accepted=2 or accepted=3))a )";
        } else {
            $sql = "UPDATE Games SET bet2='$redpost_id', accepted=3 WHERE player1='$player1' AND player2='$player2' AND subreddit='$subreddit' AND time_created=(SELECT max FROM (SELECT MAX(time_created) max FROM Games WHERE player1='$player1' AND player2='$player2' AND subreddit='$subreddit' and (accepted=2 or accepted=3))a )";
        }
    } else {
        $redpost_id = $_POST["redpost_id"];
        $subreddit = $_POST["subreddit"];
        $curr_time = $_SESSION["curr_time"];
        $sql = "INSERT INTO Investments VALUES ('$uname', '$redpost_id', '$subreddit', $curr_time)";
    }
    $result = $conn->query($sql) or die (exit('Query failed'));
    $conn->close();
    exit('auth=success');
}
?>
