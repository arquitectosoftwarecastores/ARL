<div class="container">
	<div class="row">
		<div class="col-md-12 centrado">
			<?php
			if ($allRecs > 0) {
			?>

				<label>Registros: <?php echo ($inicia + 1); ?> - <?php echo $lastRec; ?> de <?php echo $allRecs; ?>.</label>
				<label>P&aacute;gina: <?php echo $currentPage + 1; ?> de <?php echo $allPags; ?></label>


			<?php
			}
			if ($allRecs > $rxp) {
			?>

				<nav aria-label="Page navigation example">
					<ul class="pagination justify-content-center">

						<li class="page-item" <?php if ($currentPage <= 0) echo "style='display:none;' ";  ?>>
							<a class="page-link" href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo (0) * $rxp; ?>" tabindex=" -1" aria-disabled="true">Primera</a>
						</li>

						<li class="page-item" <?php if ($currentPage == 0) echo "style='display:none'"; ?>>
							<a class="page-link" href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($currentPage - 1) * $rxp; ?>" tabindex="-1" aria-disabled="true">Anterior</a>
						</li>

						<?php
						for ($i = $startPage; $i < $lastPage; $i++) {
							$sigRec = $i * $rxp;
						?>
							<li class="page-item <?php if ($currentPage == $i) echo "disabled"; ?>">
								<a class="page-link" href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo $i * $rxp; ?>">
									<?php
									if ($currentPage != $i) {
										echo $i + 1;
									} else {
										echo "<strong>" . ($i + 1) . "</strong>";
									}
									?>
								</a>
							</li>
						<?php
						}
						?>

						<li class="page-item" <?php if ($currentPage + 1 >= $allPags) echo "style='display:none'"; ?>>
							<a class="page-link" href=" ?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($currentPage + 1) * $rxp; ?>">Siguiente</a>
						</li>

						<li class="page-item" <?php if ($currentPage + 1 >= $allPags) echo "style='display:none;' ";  ?>>
							<a class="page-link" href=" ?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($allPags - 1) * $rxp; ?>">Última</a>
						</li>
					</ul>
				</nav>

			<?php  } ?>
		</div>
	</div>
</div>