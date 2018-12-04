<?php
session_start();
$servername = "localhost";
$username = "tblazek";
$password = "goirish";
$dbname = "tblazek";

include_once __DIR__ . '/OnlineFriends.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uname = $_POST['userName'];
$pass = $_POST['password'];

if($_POST['request'] == 'login'){
    $sql = "SELECT * FROM Players WHERE username='$uname' AND password=PASSWORD('$pass')";
    $result = $conn->query($sql);
    /*while($row = $result->fetch_assoc()){
        if($row['username'] == $_POST['userName']){
            if($row['password'] == $_POST['password']){
                $_SESSION['username'] = $row['username'];
                exit('auth=success');
            }
        }
    }*/
    //$conn->close();
    if($result->num_rows > 0){
        $app = new OnlineFriends();
        $app->updateLastCheckin($uname);

        $_SESSION['username'] = $uname;
        $_SESSION['is_loggedin'] = true;
        setcookie('username', $uname, 0, '/', null, false, true);
        setcookie('is_loggedin', true, 0, '/', null, false, true);
        exit('auth=success');
    }else{
        exit('auth=failure');
    }
}

if($_POST['request'] == 'register'){
    /*while($row = $result->fetch_assoc()){
        if($row['username'] == $_POST['userName']){
            exit('reg=userExists');
        }
    }*/
    $name = $_POST['name'];
    $sql = "INSERT INTO Players (username, name, password) VALUES ('$uname', '$name', PASSWORD('$pass'))";
    if($conn->query($sql) === TRUE){
        exit('reg=success');
    }else{
        exit('reg=userExists');
    }
    
    exit('reg=success');
}

?>
