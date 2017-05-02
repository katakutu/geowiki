<?php

	die();

	include "../library/functions.php";

	$conn = init_sql();
	$conn2 = init_sql();

    $query = "SELECT id, raw, wikipedia FROM texts WHERE raw IS NOT null AND html IS NOT null LIMIT 0, 500";
    $result = $conn->query($query);
    $geonames = array();

    $i = 0;

    $stmt = $conn2->prepare("SELECT lat, lng, wikipedia, '' as `to` FROM geonames WHERE wikipedia = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM redirects INNER JOIN geonames ON geonames.wikipedia = redirects.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM locations INNER JOIN geonames ON geonames.wikipedia = locations.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL LIMIT 0, 1");
    $stmt2 = $conn2->prepare("UPDATE texts SET loc = ? WHERE id = ?");


    while ($row = $result->fetch_assoc()) {

    	$fulltext = $row["raw"];
    	$title = $row["wikipedia"];
    	$article_id = $row["id"];

		preg_match_all("/\{\{(.*?)\}\}/", $fulltext, $out);

	    $remove = $out[1];
	    $remove = array_unique($remove);

		foreach ($remove as $item) {

			$fulltext = str_replace("{{".$item."}}", "", $fulltext);


		};
		
		preg_match_all("/\[\[(.*?)\]\]/", $fulltext, $out);

		$fulltext = str_replace("\n", "<br>", $fulltext);

		$conn = init_sql();



	    $extracted = $out[1];
	    $extracted = array_unique($extracted);

		$locCount = 0;
		$linkCount = 0;
		$totalLink = 0;

		$locations = array();

		foreach ($extracted as $item) {

			$itemArray = explode("|", $item);

			if (count($itemArray) == 2) {

				$wikiTitle = $itemArray[0];
				$displayTitle = $itemArray[1];

			} else {

				$wikiTitle = $itemArray[0];
				$displayTitle = $itemArray[0];

			}



			$wikiTitle = str_replace(" ", "_", $wikiTitle);

	        $stmt->bind_param("sss", $wikiTitle, $wikiTitle, $wikiTitle);
	        $stmt->execute();
			$stmt->store_result();
			$num_of_rows = $stmt->num_rows;
	        
	        $stmt->bind_result($lat, $lng, $wikipedia, $to);


	        if ($num_of_rows > 0) {

	        	$class = "loc";
	        	$gn = 0;
	        	$loc = "";

			   while ($stmt->fetch()) {

					$loc = $lat.",".$lng;

			   }

			   if ($to == "") {

			   		$to = $wikipedia;

			   }

			   $locations[] = array(
			   		"source" => $wikiTitle,
			   		"redirect" => $to,
			   		"display" => $displayTitle,
			   		"location" => $loc,
			   		"position" => strpos($fulltext, $displayTitle)
			   	);

				//$fulltext = str_replace("[[".$item."]]", "<a gn='$gn' loc='$loc'".'source="'.$wikiTitle.'" matched="'.$to.'" class="'.$class.'" href="https://en.wikipedia.org/wiki/'.$wikiTitle.'" target="_blank">[['.$displayTitle."]]</a>", $fulltext);

				//$locCount++;


	        }



			$totalLink++;



		}

		echo $title;
		echo "\n";
		//print_r(($locations));

		$string = "";

		foreach ($locations as $location) {

			$string .= $location["source"];
			$string .= "\t";
			$string .= $location["redirect"];
			$string .= "\t";
			$string .= $location["display"];
			$string .= "\t";
			$string .= $location["location"];
			$string .= "\t";
			$string .= $location["position"];
			$string .= "\n";

		}

		$string = trim($string);

		//echo $string;
		//echo "\n";
		//echo "\n";

		$stmt2->bind_param("si", $string, $article_id);
		$stmt2->execute();			

		$i++;
		echo $i;
		echo "\n";			


    };	



	$stmt->close();

?>