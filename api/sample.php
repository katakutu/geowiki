<!DOCTYPE html>
<html>
	<head>


		<meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	    <!-- BOOTSTRAP V4 -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>		

		<!-- FONT AWESOME -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

		<!-- DT -->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>		

		<!-- CUSTOM -->
		<link rel="stylesheet" type="text/css" href="/css/style.css">
		<script type="text/javascript" src="/js/script.js"></script>

	</head>
	<body session="<?= $session_id ?>">


		<div class="container-fluid" id="body">

			<div class="row">

				<div class="col-xl-12">

					<form>

						<div class="form-group">
							<textarea class="form-control" id="api_data" rows="10" placeholder="Enter-separated list of locations"></textarea>
						</div>


						<div class="form-group">
							<input class="form-control" id="api_context" placeholder="Lat/lng value for contextual search (ex. 43.7,-79.416)">
						</div>

						<button type="submit" id="api_submit" class="btn btn-primary">Submit</button>		


						<div class="card" id="api_endpoint">

							<div class="card-header">
								cURL
							</div>

							<div class="card-block">

								<samp id="api_endpoint_raw">-</samp>

							</div>

						</div>				

						<div class="card" id="api_input">

							<div class="card-header">
								Input
							</div>

							<div class="card-block">

								<samp id="api_input_raw">-</samp>

							</div>

						</div>							


						<div class="card" id="api_output">

							<div class="card-header">
								Output (Raw)
							</div>

							<div class="card-block">

								<samp id="api_output_raw">-</samp>

							</div>

						</div>	


						<div class="card" id="api_output_array">

							<div class="card-header">
								Output (Clean)
							</div>

							<div class="card-block">

								<pre id="api_output_array_raw">-</pre>

							</div>

						</div>	


					</form>		

				</div>

			</div>

		</div>

	</div>

</body>