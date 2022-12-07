<div class="container">
<div class="row">
	<div class="col-md-12 centrado"> 
<?php 
if ($allRecs > 0) {
?>

		<label>Registros: <?php echo($inicia+1); ?>-<?php echo $lastRec; ?> de <?php echo $allRecs; ?></label>
		<label>P&aacute;gina: <?php echo $currentPage+1; ?> de <?php echo $allPags-1; ?></label>


<?php 
    }
  if ($allRecs > $rxp)
    {
?>
		<nav>
  		<ul class="pagination">
    		<li <?php if ($currentScr-1 < 0) echo "style='display:none'"; ?> >
      		<a href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($currentScr-1)*$pagsXscr*$rxp; ?>" aria-label="Previous">
        		<span aria-hidden="true">&laquo;</span>
      		</a>
    		</li>
			<?php
				for ($i=$startPage; $i<$lastPage; $i++)
					{
						$sigRec = $i*$rxp;
			?>   
			<li><a href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo $i*$rxp; ?>"><?php echo $i+1; ?></a></li>
			<?php
					}
			?>
    		<li <?php if ($currentScr+1 > $lastScr-1) echo "style='display:none'"; ?>>
      		<a href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($currentScr+1)*$pagsXscr*$rxp; ?>" aria-label="Next">
        		<span aria-hidden="true">&raquo;</span>
      		</a>
    		</li>
  		</ul>
		</nav>

		<nav>
  		<ul class="pager">
    		<li <?php if ($currentPage <= 0) echo "style='display:none;' ";  ?>><a href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo (0)*$rxp; ?>">Primera</a></li>
    		<li <?php if ($currentPage-1 < 0) echo "style='display:none;' ";  ?>><a href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($currentPage-1)*$rxp; ?>">Anterior</a></li>    		
    		<li <?php if ($currentPage+1 > $allPags-1) echo "style='display:none;' ";  ?>><a href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($currentPage+1)*$rxp; ?>">Siguiente</a></li>
    		<li <?php if ($currentPage >= $allPags-1) echo "style='display:none;' ";  ?>><a href="?<?php echo $href_inicia; ?>&amp;inicia=<?php echo ($allPags-1)*$rxp; ?>">Última</a></li>
  		</ul>
		</nav>		
		

<?php  } ?>
	</div>
</div>
</div>