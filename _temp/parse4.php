<?php

	die();
	
	include "./library/functions.php";
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time', 9999999);

	$filename = "./data/geo_coordinates_en.ttl";

	$conn = init_sql();

    $stmt = $conn->prepare("SELECT id, wikipedia FROM geonames WHERE wikipedia=? LIMIT 0, 1");
    $stmt2 = $conn->prepare("INSERT INTO geonames (`wikipedia`, `lat`, `lng`) VALUES (?, ?, ?)");
    $stmt3 = $conn->prepare("UPDATE geonames SET lat = ?, lng = ? WHERE wikipedia = ?");

    $i = 0;

	$handle = fopen($filename, "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {

	    	$lineArray = explode(" ", $line);

	    	if ($lineArray[1] == "<http://www.georss.org/georss/point>") {


		    	//print_r($lineArray);

	    		$wikipedia = str_replace("<http://dbpedia.org/resource/", "", $lineArray[0]);
	    		$wikipedia = str_replace(">", "", $wikipedia);
	    		$wikipedia = trim($wikipedia);

		    	$lat = (float)str_replace('"', "", $lineArray[2]);
		    	$lng = (float)$lineArray[3];

		        $stmt->bind_param("s", $wikipedia);
		        $stmt->execute();
				$stmt->store_result();	        
				$num_of_rows = $stmt->num_rows;

				if ($num_of_rows > 0) {

					echo "UPDATE";
					echo "\n";
			    	$stmt3->bind_param("dds", $lat, $lng, $wikipedia);
				    $stmt3->execute();					

				} else {

					echo "CREATE";
					echo "\n";
			    	$stmt2->bind_param("sdd", $wikipedia, $lat, $lng);
				    $stmt2->execute();		

				}

				$i++;

				echo $i;
				echo "\n";

				/*

		    	echo $wikipedia;
		    	echo "\n";
		    	echo $lat;
		    	echo "\n";
		    	echo $lng;
		    	echo "\n";
		    	echo "\n";

			    $stmt2->bind_param("sdd", $wikipedia, $lat, $lng);
			    $stmt2->execute();			    	

		    	continue;*/

	    	}



	    }

	    fclose($handle);
	} else {
	    // error opening the file.
	} 

	$stmt->close();
	$stmt2->close();
	$stmt3->close();

	echo "DONE";


	//echo $data;

	//$lines = explode("\n", $data);


	//$conn = init_sql();



    $conn->close();

    //print_r($i);

?>