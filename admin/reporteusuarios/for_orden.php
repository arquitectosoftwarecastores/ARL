            	<?php if(isset($_GET["orden"]) && $_GET["orden"]==$variable."_up") { ?>
					<span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
				<?php }
				     else { ?>
					<a href="<?php echo $url."&orden=".$variable."_up"; ?>"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
			    <?php } ?>
            	<?php if(isset($_GET["orden"]) && $_GET["orden"]==$variable."_do") { ?>
					<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
				<?php }
				     else { ?>
					<a href="<?php echo $url."&orden=".$variable."_do"; ?>"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a>
			    <?php } ?>