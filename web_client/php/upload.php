<?php
session_start();
$target_dir = "../photos/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        alert("File is an image - " . $check["mime"] . "." . "\n");
        $uploadOk = 1;
    } else {
        alert("File is not an image.\n");
        $uploadOk = 0;
    }
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    alert("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    alert("Sorry, your file is too large.");
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    alert("Sorry, your file was not uploaded.");

// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        
        alert("The file has been uploaded.");

        // Change File Path in Query
        update("photos/" . basename($_FILES["fileToUpload"]["name"]));

    } else {
        alert("Sorry, there was an error uploading your file.");
    }
}

// Sends an alert in javascript whether the upload was successful or not
function alert($msg) {
    echo "<script type='text/javascript'>
    alert('$msg');
    window.location.href='http://dsg1.crc.nd.edu/cse30246/rawtoast/stage3/settings.php';
    </script>";
}
// Updates a user's photo in the Players database
function update($filepath) {
    $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database Connection Error');
    mysqli_select_db($link, 'tblazek');

    $user = $_SESSION["username"];
    $query = "UPDATE Players SET photo = '$filepath' WHERE username = '$user'";
    $result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
    
    mysqli_free_result($result);
    mysqli_close($link);
    
}
?>
