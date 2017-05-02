<!DOCTYPE html>
<html>
	<head>


		<?

			include $_SERVER["DOCUMENT_ROOT"]."/library/functions.php";

			if (!isset($_GET["id"])) {

				$session_id = uniqid();

				header("Location: ?id=".$session_id);
				die();

			} else {

				$session_id = $_GET["id"];

			}

			if (isset($_GET["article"])) {

				$article_id = $_GET["article"];

				$conn = init_sql();

			    $query = "SELECT wikipedia FROM sessions WHERE id = $article_id";
			    $result = $conn->query($query);
			    $geonames = array();

			    while ($row = $result->fetch_assoc()) {

			        $title = $row["wikipedia"];

			    }				



			} else {

				$article_id = "";
				$title = "";

			}


		?>

		<meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	    <!-- BOOTSTRAP V4 -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>		

		<!-- FONT AWESOME -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

		<!-- GMAPS -->
		 <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyAVeugQeLWsTlUB9yoQdbK5SfPaKVHU-Wg"></script>


		<!-- DT -->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>		

		<!-- CUSTOM -->
		<link rel="stylesheet" type="text/css" href="/css/style.css">
		<script type="text/javascript" src="/js/script.js"></script>

	</head>
	<body session="<?= $session_id ?>">

		<? include $_SERVER["DOCUMENT_ROOT"]."/components/navbar.php" ?>	

		<div class="container-fluid" id="body">

			<div class="row">
				<div class="col-xl-2 col-lg-3 col-md-4" id="left">

					<? include $_SERVER["DOCUMENT_ROOT"]."/components/insert.php" ?>	
					<? include $_SERVER["DOCUMENT_ROOT"]."/components/menu.php" ?>	
					
				</div>
				<div class="col-xl-10 col-lg-9 col-md-8">