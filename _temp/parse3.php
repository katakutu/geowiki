<?php

	die();

	include "./library/functions.php";
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time', 9999999);

	$filename = "./data/mappingbased_objects_en.ttl";


	$conn = init_sql();

    $stmt = $conn->prepare("SELECT id, wikipedia FROM geonames WHERE wikipedia=? LIMIT 0, 1");
    $stmt2 = $conn->prepare("INSERT INTO locations (`from`, `to`) VALUES (?, ?)");

	$handle = fopen($filename, "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {

	    	$lineArray = explode(" ", $line);

	    	if ($lineArray[1] == "<http://dbpedia.org/ontology/location>") {

				$from = str_replace("<http://dbpedia.org/resource/", "", $lineArray[0]);
				$from = str_replace(">", "", $from);
				$from = trim($from);

				$to = str_replace("<http://dbpedia.org/resource/", "", $lineArray[2]);
				$to = str_replace(">", "", $to);
				$to = trim($to);

		        $stmt->bind_param("s", $to);
		        $stmt->execute();
				$stmt->store_result();	        
				$num_of_rows = $stmt->num_rows;

				if ($num_of_rows > 0) {

					echo $from;
					echo "\n";
					echo $to;
					echo "\n";
					echo "\n";

				    $stmt2->bind_param("ss", $from, $to);
				    $stmt2->execute();					

				}

	    	}



	    }

	    fclose($handle);
	} else {
	    // error opening the file.
	} 

	$stmt->close();
	$stmt2->close();

	echo "DONE";


	//echo $data;

	//$lines = explode("\n", $data);


	//$conn = init_sql();



    $conn->close();

    //print_r($i);

?>