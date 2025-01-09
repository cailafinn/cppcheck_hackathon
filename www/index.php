<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles.css">
<style>
	table {
	float: left;
	width: 50%;
	padding: 10px;
}
</style>
</head>
<body>
	<h1>Mantid CppCheck Hackathon</h1>

	<h2><a href="rules.html"> The Rules</a> </h2>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$connection = pg_connect("dbname=cppcheck_hackathon");
if (false == $connection) {
	echo '<span>Failed to connect to database!</span>';
}

$name_query = "SELECT * FROM groups;";
$name_result = pg_query($connection, $name_query);

$col_counter = 1;
while ($name_row = pg_fetch_array($name_result, null, PGSQL_ASSOC)) {
	if ($col_counter % 2 == 1) {
		echo "<div>";
	}
	echo "<table><tr>";
	echo "<thead><th colspan=3>" . $name_row['name'] . "</th></thead>";
	echo "</tr>";
	echo "<tr> <th>Set ID</th><th>Completed</th><th>Merged By</th> </tr>";
	
	$set_query = "SELECT sets.id, completed, name FROM sets LEFT JOIN groups ON sets.merged_by=groups.id WHERE assigned_to =" . $name_row['id'] . "ORDER BY completed";
	$set_result = pg_query($connection, $set_query);

	while($set_row = pg_fetch_array($set_result, null, PGSQL_ASSOC)) {
		$set_link = "<a href='suppressionSet.php?setid=" . $set_row['id'] . "'> " . $set_row['id'] . "</a>";
		$background_colour = 'lime';
		if ($set_row['completed'] == 'f') {
			$background_colour='yellow';
		}
		echo "<tr style='background-color: $background_colour'> <td>" . $set_link  . "</td> <td>" .  $set_row['completed'] . "</td> <td>" . $set_row['name'] . "</td></tr>";
	}

	$completed_query = "SELECT count(*) FROM sets WHERE assigned_to=" . $name_row['id']  . "AND completed=TRUE;";
	$completed_result = pg_fetch_array(pg_query($connection, $completed_query), null, PGSQL_ASSOC);

	$merged_query = "SELECT count(*) FROM sets WHERE merged_by=" . $name_row['id']  . "AND completed=TRUE;";
	$merged_result = pg_fetch_array(pg_query($connection, $merged_query), null, PGSQL_ASSOC);

	echo "<tfoot> <tr> <td> Fix Score: " . $completed_result['count'] * 3 . " </td> <td> Merge Score: " . $merged_result['count'] . " </td> <td> Total Score: " . (($completed_result['count'] * 3) + ($merged_result['count'])) . "</td> </tr> </tfoot>";
	echo "</table>";
	if ($col_counter % 2 == 0) {
		echo "</div>";
	}
	$col_counter++;
}



?>

</body>

</html>
