<?php
$servername="localhost";
$username="root";
$password="";
$dbname="pastrydb";

//Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

//Run a query
$sql = filter_input(INPUT_POST, "query");
//$rowcount = myqsli_num_rows;

if ($result = $conn->query($sql)) {
    // output data of each row
    $fields = mysqli_fetch_fields($result);
    echo "<div id=output><table><tr>";
    foreach($fields as $name) {
	    echo "<th>".$name->name."</th>";
    }
    echo "</tr>";
    while($row = mysqli_fetch_row($result)) {
	echo "<tr>";
	//foreach($fields as $name) {
	    for($x = 0; $x < mysqli_num_fields($result); $x++) {
	    	echo "<td>".$row[$x]."</td>";
		}
	//}
	echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>0 results</p>";
}

//mysqli_free_result($result);
mysqli_close($conn);

?>
