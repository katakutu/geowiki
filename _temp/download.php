<?php

	//die();

	include "./library/functions.php";
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time', 9999999);

	$filename = "./data/topical_concepts_en.ttl";

	$conn = init_sql();

    $stmt = $conn->prepare("SELECT id, wikipedia FROM texts WHERE wikipedia=? LIMIT 0, 1");
    $stmt2 = $conn->prepare("INSERT INTO texts (`wikipedia`, `raw`, `html`) VALUES (?, ?, ?)");

	$handle = fopen($filename, "r");

	$i = 0;

	if ($handle) {
	    while (($line = fgets($handle)) !== false) {

	    	$lineArray = explode(" ", $line);

	    	if ($lineArray[1] == "<http://purl.org/dc/terms/subject>") {

	    		$wikipedia = str_replace("<http://dbpedia.org/resource/", "", $lineArray[2]);
	    		$wikipedia = str_replace(">", "", $wikipedia);
	    		$wikipedia = trim($wikipedia);


		        $stmt->bind_param("s", $wikipedia);
		        $stmt->execute();
				$stmt->store_result();	        
				$num_of_rows = $stmt->num_rows;

				if ($num_of_rows == 0) {

					$url = "https://en.wikipedia.org/w/api.php?action=query&titles=".($wikipedia)."&prop=revisions&rvprop=content&format=json";

					$content = file_get_contents($url);
					$data = json_decode($content);

					$fulltext = reset($data->query->pages)->revisions[0]->{"*"};

					$url = "https://en.wikipedia.org/w/api.php?action=parse&prop=text&format=json&page=".($wikipedia);
					$content = file_get_contents($url);
					$data = json_decode($content);

					$plaintext = $data->parse->text->{"*"};

			    	echo $wikipedia;
			    	echo "\n";
			    	echo strlen($fulltext);
			    	echo "\n";
			    	echo strlen($plaintext);
			    	echo "\n";
			    	echo "\n";


				    $stmt2->bind_param("sss", $wikipedia, $fulltext, $plaintext);
				    $stmt2->execute();		    	

			    	$i++;		


			    	if ($i == 1) {

			    		break;

			    	} else {

		    			continue;

			    	}


				}


	


	    	}



	    }

	    fclose($handle);
	} else {
	    // error opening the file.
	} 

	//$stmt->close();
	$stmt2->close();

	echo "DONE";


	//echo $data;

	//$lines = explode("\n", $data);


	//$conn = init_sql();



    $conn->close();

    //print_r($i);

?>