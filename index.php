<? include $_SERVER["DOCUMENT_ROOT"]."/header.php" ?>	

	<? if ($title == "") { ?>

	<div class="jumbotron">
		<h1 class="display-3">GeoWiki</h1>
		<p class="lead">Automatic Location Parser for Wikipedia</p>
		<hr class="my-4">
		<p>Start by adding your first Wikipedia article.</p>
		<p class="lead">
			<a class="btn btn-primary btn-lg" href="/?id=58bf967790b9b&article=20" role="button">Sample session</a>
		</p>		
	</div>		

	<? } else { ?>			

		<?

			$gnList = array();

			$searchTitle = str_replace("+", "%2B", $title);

			$url = "https://en.wikipedia.org/w/api.php?action=query&titles=".($searchTitle)."&prop=revisions&rvprop=content&format=json";

			$content = file_get_contents($url);
			$data = json_decode($content);

			$fulltext = reset($data->query->pages)->revisions[0]->{"*"};

			$url = "https://en.wikipedia.org/w/api.php?action=parse&prop=text&format=json&page=".($searchTitle);
			$content = file_get_contents($url);
			$data = json_decode($content);

			$plaintext = $data->parse->text->{"*"};

			preg_match_all("/\{\{(.*?)\}\}/", $fulltext, $out);

	        $remove = $out[1];
	        $remove = array_unique($remove);

			foreach ($remove as $item) {

				$fulltext = str_replace("{{".$item."}}", "", $fulltext);


			};
			
			preg_match_all("/\[\[(.*?)\]\]/", $fulltext, $out);

			$fulltext = str_replace("\n", "<br>", $fulltext);

			$conn = init_sql();

	        $stmt = $conn->prepare("SELECT lat, lng, wikipedia, '' as `to` FROM geonames WHERE wikipedia = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM redirects INNER JOIN geonames ON geonames.wikipedia = redirects.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL UNION SELECT lat, lng, wikipedia, `to` FROM locations INNER JOIN geonames ON geonames.wikipedia = locations.to WHERE `from` = ? AND lat IS NOT NULL AND lng IS NOT NULL LIMIT 0, 1");


	        $extracted = $out[1];
	        $extracted = array_unique($extracted);

			$locCount = 0;
			$linkCount = 0;
			$totalLink = 0;

			$wikiList = array();

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

				   $obj = array();
				   $obj["loc"] = $loc;
				   $obj["wikiTitle"] = $wikiTitle;
				   $obj["to"] = $to;
				   $obj["displayTitle"] = $displayTitle;
				   $obj["class"] = $class;
				   $obj["item"] = $item;

				   $wikiList[] = $obj;

					$fulltext = str_replace("[[".$item."]]", "<a gn='$gn' loc='$loc'".'source="'.$wikiTitle.'" display="'.$displayTitle.'" matched="'.$to.'" class="'.$class.'" href="https://en.wikipedia.org/wiki/'.$wikiTitle.'" target="_blank">[['.$displayTitle."]]</a>", $fulltext);

					$locCount++;



		        } else {

		        	$class = "link";
		        	$gn = "";
		        	$loc = "";


					$fulltext = str_replace("[[".$item."]]", "<a gn='$gn' loc='$loc' name='".$wikiTitle."' class='$class' href='https://en.wikipedia.org/wiki/".$wikiTitle."' target='_blank'>[[".$item."]]</a>", $fulltext);

					$linkCount++;


		        }



				$totalLink++;



			}

			$stmt->close();

		?>

	<div class="row">



		<div class="col-6">

		<ul class="nav nav-pills mb-4" id="switch">
			<li class="nav-item">
			<a class="nav-link active" href="#fulltext">Wikitext</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="#html">HTML</a>
			</li>			
			<li class="nav-item">
			<a class="nav-link" href="#plaintext">Plaintext</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="#occurrence">Occurrences</a>
			</li>
		</ul>



			<div id="fulltext">

			<?= $fulltext ?>

			</div>

			<div id="html">

			<?= $plaintext ?>

			</div>

			<div id="plaintext">

			<?= $plaintext ?>

			</div>

			<div id="occurrence">

				<table class="table table-sm" id="occurrence_table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Offsets (in plaintext)</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>

			</div>

		</div>
		<div class="col-6" id="details">

			<table class="table table-sm" id="summary">
				<thead>
					<tr>
						<th>Occurrence</th>
						<th>Source</th>
						<th>Location</th>
						<th>Lat/Lng</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>

		</div>
	</div>

	<? } ?>

<? include $_SERVER["DOCUMENT_ROOT"]."/footer.php" ?>	
