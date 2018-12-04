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

<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
    Open modal
</button>

<!-- The Modal -->
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal footer -->
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div> -->


<?php
$subreddit = "CFB";//$_GET["subreddit"];
if ($subreddit == "") {
    exit('auth=success');
}
session_start();
$servername = "localhost";
$username = "tblazek";
$password = "goirish";
$dbname = "tblazek";
$uname = "kanye";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!-- Modal Header -->
    <div class="modal-header">
        <h4 class="modal-title"><?php $subreddit ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">

<?php
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

$highest_predicted_score_query = "SELECT predicted_score FROM (SELECT predicted_score FROM Posts WHERE subreddit='" . $subreddit . "' ORDER BY created desc LIMIT 10) a ORDER BY predicted_score desc LIMIT 1";
$highest_predicted_score_query_result = $conn->query($highest_predicted_score_query) or die (exit("Query Failed"));
$highest_predicted_score_query_result_assoc = $highest_predicted_score_query_result->fetch_assoc();
$highest_predicted_score = $highest_predicted_score_query_result_assoc["predicted_score"];
while($row = $result->fetch_assoc()){
    $sub = $sub . '<button name="buttonSelected" type="button" value="'. $row["id"] .'" class="list-group-item list-group-item-action">
    <table class="table table-sm" style="height: 100px; margin-bottom: 0;">
    <tbody style="border-top: none;">
        <tr>
        <td class="align-middle" style="width: 16.66%; border-top: none;">';
    //if ($row["thumbnail"] != "spoiler" && $row["thumbnail"] != "self" && $row["thumbnail"] != "" && $row["thumbnail"] != NULL && $row["thumbnail"] != "default" && $row["thumbnail"] != "nsfw") {
    if(strpos($row["thumbnail"], "http") === 0){
        $sub = $sub . '<img src="'. addslashes($row["thumbnail"]) .'" class="rounded float-left">';
    } else{
        $sub = $sub . '<img src="https://b.thumbs.redditmedia.com/yV6o42cnJzuzWU03wj_eCjJE1Y8OnlYslVW2OYT7oFQ.jpg" class="rounded float-left">';
    }
    $sub = $sub . '<td class="align-middle" style="text-align: left; border-top: none;">
    <ul class="list-group list-group-flush">
        <li class="list-group-item bg-transparent">';
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
?>
            </div>
        </div>
    </div>
</div>