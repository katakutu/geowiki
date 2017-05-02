<?php

	include "../library/functions.php";

	$conn = init_sql();

	$id = $_GET["id"];
	$mode = $_GET["mode"];

    $query = "SELECT * FROM texts WHERE id = $id";
    $result = $conn->query($query);



    while ($row = $result->fetch_assoc()) {

    	$content = $row[$mode];

    	if ($mode == "html") {

	    	echo $content;

    	} else {

	    	echo "<pre>".$content."</pre>";

    	}

    }

?>