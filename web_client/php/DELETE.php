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

// Delete Account from Database
$uname = $_SESSION['username'];

if(isset($_POST['request']) && $_POST['request'] == "deleteUser"){
    
    // Delete from Players
    $sql = "DELETE FROM Players WHERE username = '$uname'";
    $result = $conn->query($sql);

    // Delete from Investments
    $sql = "DELETE FROM Investments WHERE username = '$uname'";
    $result = $conn->query($sql);

    // Return Success
    exit('change=success');
}

?>
