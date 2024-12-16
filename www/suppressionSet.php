<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles.css">
<style>

table {
	margin-left: auto;
	margin-right: auto;
        width: 80%;
        padding: 10px;
}

th, td {
        width: 33%;
	border: 1px solid;
	white-space: nowrap;
	padding: 10px;
}

.code {
        font-family: 'Consolas', monospace;
}


</style>
</head>
<body>

<?php

$connection = pg_connect("dbname=cppcheck_hackathon");
if (false == $connection) {
        echo '<div>Failed to connect to database!</div>';
}

$set_id = $_GET['setid'];

$query_string = "SELECT suppressions.issue, suppressions.file, suppressions.line, sets.completed FROM suppressions INNER JOIN sets ON sets.id=suppressions.set WHERE set=$1;";
$suppression_query = pg_prepare($connection, "suppression", $query_string);
$suppression_result = pg_execute($connection, "suppression", array($set_id));

echo "<h1> Fixes Required for Suppression Set " . $set_id . "</h1>";
echo "<table><tr> <th> Issue </th> <th> File </th> <th> Line </th> </tr>";

while($suppression = pg_fetch_array($suppression_result, null, PGSQL_ASSOC)) {
        $background_colour = 'lime';
        if ($suppression['completed'] == 'f') {
                $background_colour='yellow';
        }
	echo "<tr style='background-color: $background_colour'><td class=code>" . $suppression['issue'] . "</td><td class=code>" . $suppression['file'] . "</td><td>" . $suppression['line'] . "</td></tr>";
}


echo "</table>";
?>

</body>
</html>
