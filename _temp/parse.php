<?php

	die();
	
	include "./library/functions.php";
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time', 9999999);

	$filename = "./data/geonames_links_en.ttl";

	$conn = init_sql();

    $stmt2 = $conn->prepare("INSERT INTO geonames (`wikipedia`, `geonames`) VALUES (?, ?)");

	$handle = fopen($filename, "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {

	    	$lineArray = explode(" ", $line);


	    	if ($lineArray[1] == "<http://www.w3.org/2002/07/owl#sameAs>") {


		    	print_r($lineArray);

	    		$wikipedia = str_replace("<http://dbpedia.org/resource/", "", $lineArray[0]);
	    		$wikipedia = str_replace(">", "", $wikipedia);
	    		$wikipedia = trim($wikipedia);


	    		$geonames = str_replace("<http://sws.geonames.org/", "", $lineArray[2]);
	    		$geonames = str_replace("/>", "", $geonames);
	    		$geonames = trim($geonames);


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

	$stmt2->close();

	echo "DONE";


	//echo $data;

	//$lines = explode("\n", $data);


	//$conn = init_sql();



    $conn->close();

    //print_r($i);

?>