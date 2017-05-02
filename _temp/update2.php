<?php

	die();

	include "./library/functions.php";
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time', 9999999);


	$conn = init_sql();


    $query = "SELECT geonames, wikipedia FROM geonames WHERE geonames IS NOT null AND lat IS NULL AND lng IS NULL";
    $result = $conn->query($query);
    $geonames = array();

    $stmt2 = $conn->prepare("UPDATE geonames SET lat = ?, lng = ? WHERE geonames = ?");

    $i = 0;

    while ($row = $result->fetch_assoc()) {

        //$title = $row["wikipedia"];

    	$gn = $row["geonames"];


		$content = file_get_contents("http://api.geonames.org/get?geonameId=".$gn."&username=chriskkim");
		$xml = simplexml_load_string($content);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);

		$lat = "";
		$lng = "";

		if (isset($array["lat"]) && isset($array["lng"])) {

			$lat = $array["lat"];
			$lng = $array["lng"];    	

			$stmt2->bind_param("ddd", $lat, $lng, $gn);
			$stmt2->execute();			

	    	$i++;
	    	echo $i;
	    	echo "\n";	

		}





		/*
		$stmt2->bind_param("ddd", $lat, $lng, $gn);
		$stmt2->execute();			

    	$i++;
    	echo $i;
    	echo "\n";			
		*/


    }				

    $stmt->close();

?>