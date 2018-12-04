<html>
<head>
<title>You can haz webpage too</title>
</head>
<body>
<form action="nmarcopo_ageaction.php" method="post">
Enter your age: <input type="textbox" name="age"/>
</form>

<?php
// connecting to database
$link = mysqli_connect('localhost', 'nmarcopo', 'kanyewest') or die ('Database connection error');
mysqli_select_db($link, 'nmarcopo');

$query = 'select * from user_age';
$result = mysqli_query($link, $query) or die ('Query failed' . mysql_error());
echo "<table>\n";
while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
	echo "\t<tr>\n";
	foreach($tuple as $col_val){
		echo "\t\t<td> $col_val </td>\n";
	}
	echo "\t</tr>\n";
}

mysqli_free_result($result);
mysqli_close($link);

?>

</body>
</html>
