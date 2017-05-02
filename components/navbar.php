<nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">
	<button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler navbar-toggler-right" data-target="#navbarSupportedContent" data-toggle="collapse" type="button"><span class="navbar-toggler-icon"></span></button> <a class="navbar-brand" href="/">

		

		<? if ($title != "") { ?>

			GeoWiki: <?= rawurldecode($title) ?>

		<? } else { ?>

			GeoWiki

		<? } ?>


	</a>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">

		</ul>



		<? if ($title != "") { ?>


			<div class="form-inline my-2 my-lg-0">

				<a href="https://en.wikipedia.org/wiki/<?= $title ?>" target="_blank"  role="button" class="btn btn-outline-success">View on Wikipedia</a>
				<button class="btn btn-outline-danger" id="delete_article" db="<?= $article_id ?>">Delete</button>

			</div>

		<? } ?>

	</div>
</nav>	