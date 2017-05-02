<?php

	$sql_servername = "geowiki.ckprototype.com";
	$sql_username = "root";
	$sql_password = "";
	$sql_dbname = "geowiki";

	function init_sql(){

		global $sql_servername;
		global $sql_username;
		global $sql_password;
		global $sql_dbname;

		$dbconn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
		$dbconn->set_charset("utf8");

		return $dbconn;

	}

	function extractAmbiguity($title){

		$conn = init_sql();

		$searchTitle = str_replace("+", "%2B", $title);
		$searchTitle = $searchTitle."_(disambiguation)";

		$output = array();


		$url = "https://en.wikipedia.org/w/api.php?action=query&titles=".($searchTitle)."&prop=revisions&rvprop=content&format=json";

		$content = file_get_contents($url);
		$data = json_decode($content);

		if (isset(reset($data->query->pages)->revisions[0])) {

			$fulltext = reset($data->query->pages)->revisions[0]->{"*"};

			preg_match_all("/\[\[(.*?)\]\]/", $fulltext, $out);

	        $extracted = $out[1];
	        $extracted = array_unique($extracted);


	        $stmt = $conn->prepare("SELECT lat, lng, wikipedia, '' as `to` FROM geonames WHERE wikipedia = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM redirects INNER JOIN geonames ON geonames.wikipedia = redirects.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM locations INNER JOIN geonames ON geonames.wikipedia = locations.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL LIMIT 0, 1");


			$locCount = 0;
			$linkCount = 0;
			$totalLink = 0;

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
				//echo "<br>";

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

					$obj = array();
					$obj["name"] = $wikiTitle;
					$obj["location"] = array($loc);

					$locCount++;

					$output[] = $obj;



		        } else {

		        	$class = "link";
		        	$gn = "";
		        	$loc = "";


					$linkCount++;


		        }



				$totalLink++;



			}

			return $output;


		} else {

			return false;

		}





	
		$stmt->close();





	}

?>