<html>
<head>
<title> You can have webpage too </title>
<script type="text/javascript">
document.write("Hello World! <br/><?php echo 'Tim';?> <a href=\"http://asdf.com\">asdf</a>")
</script>
</head>
<body>
Hello Class please visit my webpage <a href="http://nd.edu">Notre Dame</a>.
Today's date is <?php echo date("m/d/Y"); ?>
<form action="tblazek_ageaction.php" method="get">
Enter your age: <input type="textbox" name="age"/>
</form>

<?php
$link = mysqli_connect('localhost', 'tblazek', 'goirish') or die ('Database connection error');  // last argument is the password or die is error checking
mysqli_select_db($link, 'tblazek');

$query = 'select * from user_age';
$result = mysqli_query($link, $query) or die ('Query Failed: ' . mysql_error());
echo "<table>\n";

while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach( $tuple as $col_val) {
        echo "\t\t<td> $col_val </td>\n";
    }
    echo "\t</tr>\n";
}
mysqli_free_result($result);
mysqli_close($link);
?>
</body>
</html>
