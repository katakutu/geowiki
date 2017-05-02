<?

	function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);

	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;

	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
	    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return ($angle * $earthRadius) / 1000;
	}

	include $_SERVER["DOCUMENT_ROOT"]."/library/functions.php";

	//$input = '{"query_names":["Paris","London","Marseilles","Heaven"]}';
	$input = $_POST["data"];
	$array = json_decode($input, true);

	$conn = init_sql();

    $stmt = $conn->prepare("SELECT lat, lng, wikipedia, '' as `to` FROM geonames WHERE wikipedia = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM redirects INNER JOIN geonames ON geonames.wikipedia = redirects.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM locations INNER JOIN geonames ON geonames.wikipedia = locations.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL");

    $unknown = array();
    $found = array();

    $array["query_names"] = array_unique($array["query_names"]);

    $contextLat = null;
    $contextLng = null;

    if (count($array["context_locations"]) > 0) {

		$location = explode(",", $array["context_locations"][0]["location"][0]);

    	$contextLat = $location[0];
    	$contextLng = $location[1];

    }


    foreach ($array["query_names"] as $item) {

		$item = str_replace(" ", "_", $item);

	    $stmt->bind_param("sss", $item, $item, $item);
	    $stmt->execute();

		$stmt->store_result();
		$num_of_rows = $stmt->num_rows;
        $stmt->bind_result($lat, $lng, $wikipedia, $to);

        if ($num_of_rows > 0) {

			$obj = array();
			$obj["name"] = $item;
			$obj["location"] = array();

			while ($stmt->fetch()) {

				$loc = $lat.",".$lng;

				$obj["location"][] = $loc;

			}

		   	$found[] = $obj;

    		$result = extractAmbiguity($item);

    		if ($result != false && count($result) > 0) {

    			foreach ($result as $item) {

    				$pass = false;

    				foreach ($found as $i=>$obj) {

    					if ($obj["name"] == $item["name"]) {

    						$pass = true;
    						$obj["location"][] = $item["location"][0];
    						$obj["location"] = array_unique($obj["location"]);

    						$found[$i] = $obj;

    					}

    				}

    				if ($pass == false) {

	    				$found[] = $item;

    				}


    			}

    		}


        } else {

        	$unknown[] = $item;

        }



    }

	$stmt->close();

	if ($contextLat != null && $contextLng != null) {

		foreach ($found as $i=>$item) {

			$testLocation = explode(",", $item["location"][0]);
			$testLat = $testLocation[0];
			$testLng = $testLocation[1];

			$result = haversineGreatCircleDistance($contextLat, $contextLng, $testLat, $testLng);

			$item["distance"] = $result;
			$found[$i] = $item;

		}

		function cmp($a, $b)
		{
		    return $a["distance"] > $b["distance"];
		}

		usort($found, "cmp");


	}


    $output = array();
    $output["locations"] = $found;
    $output["unknown_locations"] = $unknown;

   	echo json_encode($output);

?>