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

div {
        color: red;
        width: 100%;
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

$suppression_query = pg_prepare($connection, "suppression", "SELECT * FROM suppressions WHERE set = $1");
$suppression_result = pg_execute($connection, "suppression", array($set_id));

echo "<table> <tr> <th colspan='3'> Fixes Required for Suppression Set " . $set_id . "</th> </tr>";
echo "<tr> <th> Issue </th> <th> File </th> <th> Line </th> </tr>";

while($suppression = pg_fetch_array($suppression_result, null, PGSQL_ASSOC)) {
	echo "<tr><td>" . $suppression['issue'] . "</td><td>" . $suppression['file'] . "</td><td>" . $suppression['line'] . "</td></tr>";
}



?>

</body>
</html>
