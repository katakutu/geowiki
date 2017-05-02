<?

	$portalNav = array(

	)

?>



<div class="list-group" id="menu">

<?

	$conn = init_sql();

    $query = "SELECT * FROM sessions WHERE session = '$session_id'";
    $result = $conn->query($query);
    $count = $result->num_rows;

    $geonames = array();

    while ($row = $result->fetch_assoc()) { ?>


		<a href="?id=<?= $session_id ?>&article=<?= $row["id"] ?>" class="list-group-item list-group-item-sm list-group-item-action <? if (urldecode($row["id"]) == $article_id) { echo "active"; } ?>">
			<h5 class="mb-1"><?= rawurldecode($row["wikipedia"]) ?></h5>
		</a>


<? } ?>

	<? foreach ($portalNav as $nav) { ?>

		<?

			$label = $nav["label"];

		?>


	<? } ?>


</div>

<!--
  <a href="#" class="list-group-item active"></a>
  <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
  <a href="#" class="list-group-item list-group-item-action">Morbi leo risus</a>
  <a href="#" class="list-group-item list-group-item-action">Porta ac consectetur ac</a>
  <a href="#" class="list-group-item list-group-item-action disabled">Vestibulum at eros</a>
-->