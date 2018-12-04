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


    $sql = "SELECT title, id, thumbnail FROM Posts WHERE subreddit='$subreddit' ORDER BY created desc LIMIT 10";
    $result = $conn->query($sql) or die (exit('Query failed'));
    $sub = "<style>
table, th, td {
    border: 1px solid black;
}
table {
    width: 50%;
    margin: auto;
}
td[id=radio] {
    width: 5px;
}
</style>";

    while($row = $result->fetch_assoc()){
        if ($row["thumbnail"] != "self" && $row["thumbnail"] != "" && $row["thumbnail"] != NULL && $row["thumbnail"] != "default" && $row["thumbnail"] != "nsfw") {
            $sub = $sub . "<input name='postradiobutton' type='radio' value=" . $row["id"] . "><img src=" . addslashes($row["thumbnail"]) . "> " . $row["title"] . "<br><br>";
        } else{
            $sub = $sub . "<input name='postradiobutton' type='radio' value=" . $row["id"] . "> " . $row["title"] . "<br><br>";
        }
    }

    $conn->close();
    if($result->num_rows != 10){
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

    $redpost_id = $_POST["redpost_id"];
    $subreddit = $_POST["subreddit"];
    $curr_time = $_SESSION["curr_time"];
    $sql = "INSERT INTO Investments VALUES ('$uname', '$redpost_id', '$subreddit', $curr_time, 0)";
    $result = $conn->query($sql) or die (exit('Query failed'));
    $conn->close();
    exit('auth=success');
}
?>
