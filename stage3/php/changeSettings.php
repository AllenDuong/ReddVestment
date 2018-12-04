<?php
session_start();
$servername = "localhost";
$username = "tblazek";
$password = "goirish";
$dbname = "tblazek";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uname = $_SESSION['username'];
$name = $_POST['name'];
$oldPass = $_POST['oldPassword'];
$pass = $_POST['password'];
$passConf = $_POST['passwordConf'];

if($_POST['request'] == 'updateUserDB'){
    if($name != ''){
        $sql = "UPDATE Players SET name='$name' where username='$uname'";
        $result = $conn->query($sql);
        echo("Name Changed.");
    }

    if($oldPass != '' && $pass != '' && $passConf != ''){
        $sql = "SELECT * FROM Players WHERE username='$uname' AND password=PASSWORD('$oldPass')";
        $result = $conn->query($sql);

        if($result->num_rows > 0){
            if($pass != '' && $pass == $passConf){
                $sql = "UPDATE Players SET password=PASSWORD('$pass') where username='$uname'";
                $result = $conn->query($sql);
            }
            exit(" Password Changed.");
        }else{
            exit(" Incorrect password - Password not changed.");
        }
    }
}

?>
