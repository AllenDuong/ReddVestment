<!-- Load header -->
<?php include 'php/sessionHeader.php';?>
<!DOCTYPE html>
<html>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<head>
  <link rel="stylesheet" href="home.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="main.js"></script>
</head>

<!--This is the navigation bar -->
<?php include 'php/header.php';?>

<body align=center>
  <!-- This is the header for settings -->
  <h1 style='text-align:center; font-size:30px'>
    Settings for <span style="color: #77A1C2"><?php echo($_SESSION['username']); ?>:</span>
  </h1>
  <div class="container" style="width:575px">
    <ul class="list-group" style="border:solid; border-width: 1px">
      <li class="list-group-item" style="border:solid; border-width: 1px">
        <body align=center>
            <!--This creates the form -->
            <form>
              <div class="form-group">
                <!-- Insert a new name -->
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Enter a New Name" onkeyup="toggleAvailableButtonSettings()">
              </div>
              <!-- Insert a new password -->
              <div class="form-group">
                <label for="oldPassword">Password</label>
                <input type="password" class="form-control" id="oldPassword" placeholder="Enter Your Current Password" onkeyup="toggleAvailableButtonSettings()">
                <br>
                <!-- Create new password -->
                <input type="password" class="form-control" id="password" placeholder="Enter a New Password" onkeyup="toggleAvailableButtonSettings()">
                <br>
                <!-- Verifies new password -->
                <input type="password" class="form-control" id="passwordConf" placeholder="Confirm New Password" onkeyup="toggleAvailableButtonSettings()">
              </div>
              <button type="submit" class="btn btn-primary" onclick="changeButtonClick()">Change Selected</button>
            </form>
        </body>
      </li>
      <!-- Upload Profile Pic -->
      <li class="list-group-item" style="border:solid; border-width: 1px">
        <!-- Insert new file block -->
        <form action="php/upload.php" method="post" enctype="multipart/form-data">
            <label for="fileToUpload">Select Profile Image to Upload:</label><br>
            <div class="input-group">
              <div class="custom-file">
                <input type="file" class="custom-file-input" name="fileToUpload" id="fileToUpload">
                <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
              </div>
            </div> <br>
            <input type="submit" class="btn btn-primary" value="Upload Image" name="submit">
        </form>
      </li>
      <!-- Delete Account -->
      <li class="list-group-item" style="border:solid; border-width: 1px">
        <form action="php/DELETE.php">
            <label for="DELETE" style="font-weight: bold; color: #E00000; font-size: 22px">DELETE YOUR ACCOUNT?! FOREVER?!</span>
            </label>
            <button type="button" class="btn btn-danger float-right" name="DELETE" onclick="deleteAccount()">DELETE</button>
        </form>
      </li>
    </ul>
  </div>
  </form>
</body>

<html>
