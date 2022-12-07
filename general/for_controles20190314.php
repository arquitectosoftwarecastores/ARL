<div class="container">
    <div class="row"> 
        <div class="col-md-2">
        	<p>Registros por página</p>
        </div>
        <div class="col-md-2">
    		<select id="rxp" class="form-control" >
    			<option value="5"   <?php if ($rxp == 5) { ?> selected="selected" <?php } ?>>5</option>
    			<option value="10"  <?php if ($rxp == 10) { ?> selected="selected" <?php } ?>>10</option>
    			<option value="20"  <?php if ($rxp == 20) { ?> selected="selected" <?php } ?>>20</option>
    			<option value="50"  <?php if ($rxp == 50) { ?> selected="selected" <?php } ?>>50</option>
    			<option value="100" <?php if ($rxp == 100) { ?> selected="selected" <?php } ?>>100</option>
    			<option value="500" <?php if ($rxp == 500) { ?> selected="selected" <?php } ?>>500</option>
    		</select>
        </div>
        <div class="col-md-2">
            <a href="?seccion=<?php echo $seccion;?>&amp;accion=lista"><button type="button" class="btn btn-primary btn-xs">VER TODOS</button></a>
        </div>
        <div class="col-md-2"><input type="text" id="busca" class="form-control" value="<?php if(isset($_GET["busca"])) echo $_GET["busca"]; ?>" /></div>
        <div class="col-md-1"><button type="button" id="buscar" class="btn btn-primary btn-xs">BUSCAR</button></div>    
        <div class="col-md-2"><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#nuevo">NUEVO</button></a></div>
    </div>
</div>