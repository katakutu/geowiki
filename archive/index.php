<?php

	include "../library/functions.php";

	$conn = init_sql();

    $query = "SELECT id, wikipedia FROM texts WHERE raw IS NOT null AND html IS NOT null AND loc IS NOT null AND loc != '' AND plain IS NOT null LIMIT 0, 500";
    $result = $conn->query($query);



    while ($row = $result->fetch_assoc()) {

	?>

		<h1><?= $row["id"].": ".$row["wikipedia"] ?></h1>

		<p>
			<a href="view.php?id=<?= $row["id"] ?>&mode=raw">Wikitext</a>
			<a href="view.php?id=<?= $row["id"] ?>&mode=html">HTML</a>
			<a href="view.php?id=<?= $row["id"] ?>&mode=plain">Plaintext</a>
			<a href="view.php?id=<?= $row["id"] ?>&mode=loc">Locations</a>
		</p>

	<?


    }

?>