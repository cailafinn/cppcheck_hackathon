<!DOCTYPE html>
<html>
<head>
<style>
h1 {
	text-align: center;
}

tfoot, th {
	background-color: black;
	color: white;
}

table, th, td {
	text-align: center;
}

table {
	float: left;
	width: 50%;
	padding: 10px;
}

th, td {
	width: 33%;
	border: 1px solid;
}

div {
	color: red;
	width: 100%;
}


</style>
</head>
<body>
	<h1>CppCheck Hackathon</h1>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$connection = pg_connect("dbname=cppcheck_hackathon");
if (false == $connection) {
	echo '<div>Failed to connect to database!</div>';
}

$name_query = "SELECT * FROM groups;";
$name_result = pg_query($connection, $name_query);

while ($row = pg_fetch_array($name_result, null, PGSQL_ASSOC)) {
	echo "<table><tr>";
	echo "<th colspan=3>" . $row['name'] . "</th>";
	echo "</tr>";
	echo "<tr> <th>Set ID</th><th>Completed</th><th>Merged By</th>";
	
	$set_query = "SELECT sets.id, completed, name FROM sets LEFT JOIN groups ON sets.merged_by=groups.id WHERE assigned_to =" . $row['id'];
	$set_result = pg_query($connection, $set_query);

	while($set_row = pg_fetch_array($set_result, null, PGSQL_ASSOC)) {
		$set_link = "<a href='suppressionSet.php?setid=" . $set_row['id'] . "'> " . $set_row['id'] . "</a>";
		$background_colour = 'lime';
		if ($set_row['completed'] == 'f') {
			$background_colour='yellow';
		}
		echo "<tr style='background-color: $background_colour'> <td>" . $set_link  . "</td> <td>" .  $set_row['completed'] . "</td> <td>" . $set_row['name'] . "</td></tr>";
	}

	$completed_query = "SELECT count(*) FROM sets WHERE assigned_to=" . $row['id']  . "AND completed=TRUE;";
	$completed_result = pg_fetch_array(pg_query($connection, $completed_query), null, PGSQL_ASSOC);

	$merged_query = "SELECT count(*) FROM sets WHERE merged_by=" . $row['id']  . "AND completed=TRUE;";
	$merged_result = pg_fetch_array(pg_query($connection, $merged_query), null, PGSQL_ASSOC);

	echo "<tfoot> <tr> <td> Fix Score: " . $completed_result['count'] * 3 . " </td> <td> Merge Score: " . $merged_result['count'] . " </td> <td> Total Score: " . (($completed_result['count'] * 3) + ($merged_result['count'])) . "</td> </tr> </tfoot>";
	echo "</table>";
}



?>

</body>

</html>
