<div class="container">
    <div class="row"> 
        <div class="col-md-1">
        	<p>Registros</p>
        </div>
        <div class="col-md-2">
    		<select id="rxp" class="form-control" >
    			<option value="5"   <?php if ($rxp == 5) { ?> selected="selected" <?php } ?>>5</option>
    			<option value="10"  <?php if ($rxp == 10) { ?> selected="selected" <?php } ?>>10</option>
    			<option value="20"  <?php if ($rxp == 20) { ?> selected="selected" <?php } ?>>20</option>
    			<option value="50"  <?php if ($rxp == 50) { ?> selected="selected" <?php } ?>>50</option>
    			<option value="100" <?php if ($rxp == 100) { ?> selected="selected" <?php } ?>>100</option>
    			<option value="500" <?php if ($rxp == 500) { ?> selected="selected" <?php } ?>>500</option>
    			<option value="1000" <?php if ($rxp == 1000) { ?> selected="selected" <?php } ?>>1000</option>
    			<option value="2000" <?php if ($rxp == 2000) { ?> selected="selected" <?php } ?>>2000</option>
    			<option value="5000" <?php if ($rxp == 5000) { ?> selected="selected" <?php } ?>>5000</option>
    		</select>
        </div>
        <div class="col-md-2">
            <a href="?seccion=<?php echo $seccion;?>&amp;accion=lista"><button type="button" class="btn btn-primary btn-xs">VER TODOS</button></a>
        </div>
        <div class="col-md-2"><input type="text" id="busca" class="form-control" value="<?php if(isset($_GET["busca"])) echo $_GET["busca"]; ?>" /></div>
        <div class="col-md-1"><button type="button" id="buscar" class="btn btn-primary btn-xs">BUSCAR</button></div>   
        <?php if (isset($_SESSION["altaybajadevehiculos"])) { ?> 
        <div class="col-md-2"><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#nuevo">NUEVO</button></a></div>
        <?php } ?>
        <div class="col-md-2">
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-success btn-xs" id="imprime">IMPRIMIR</button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success btn-xs" id="pdf">PDF</button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success btn-xs" id="excel">EXCEL</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$( "#imprime" ).click(function() {

    var url = "index.php?seccion=vehiculos&accion=imprime"; 
    <?php if(isset($_GET["busca"])) { ?>
      url=url +"&txt_busca=<?php echo $_GET['busca'];?>";
    <?php } ?>
    <?php if(isset($_GET["rxp"])) { ?>
      url=url +"&rxp=<?php echo $_GET['rxp'];?>";
    <?php } ?>
    <?php if(isset($_GET["orden"])) { ?>
      url=url +"&orden=<?php echo $_GET['orden'];?>";
    <?php } ?>
    <?php if(isset($_GET["estatus"])) { ?>
      url=url +"&estatus=<?php echo $_GET['estatus'];?>";
    <?php } ?>
    <?php if(isset($_GET["alerta"])) { ?>
      url=url +"&alerta=<?php echo $_GET['alerta'];?>";
    <?php } ?>
    <?php if(isset($_GET["prioridad"])) { ?>
      url=url +"&prioridad=<?php echo $_GET['prioridad'];?>";
    <?php } ?>
    <?php if(isset($_GET["from"])) { ?>
      url=url +"&from=<?php echo $_GET['from'];?>";
    <?php } ?>
    <?php if(isset($_GET["to"])) { ?>
      url=url +"&to=<?php echo $_GET['to'];?>";
    <?php } ?>
    <?php if(isset($_GET["economico"])) { ?>
      url=url +"&economico=<?php echo $_GET['economico'];?>";
    <?php } ?>
    window.open(url);

});

$( "#pdf" ).click(function() {

    var url = "admin/vehiculos/for_pdf2.php?pdf=1";
    //"index.php?seccion=vehiculos&accion=pdf"; 
    <?php if(isset($_GET["busca"])) { ?>
      url=url +"&txt_busca=<?php echo $_GET['busca'];?>";
    <?php } ?>
    <?php if(isset($_GET["rxp"])) { ?>
      url=url +"&rxp=<?php echo $_GET['rxp'];?>";
    <?php } ?>
    <?php if(isset($_GET["orden"])) { ?>
      url=url +"&orden=<?php echo $_GET['orden'];?>";
    <?php } ?>
    <?php if(isset($_GET["estatus"])) { ?>
      url=url +"&estatus=<?php echo $_GET['estatus'];?>";
    <?php } ?>
    <?php if(isset($_GET["alerta"])) { ?>
      url=url +"&alerta=<?php echo $_GET['alerta'];?>";
    <?php } ?>
    <?php if(isset($_GET["prioridad"])) { ?>
      url=url +"&prioridad=<?php echo $_GET['prioridad'];?>";
    <?php } ?>
    <?php if(isset($_GET["from"])) { ?>
      url=url +"&from=<?php echo $_GET['from'];?>";
    <?php } ?>
    <?php if(isset($_GET["to"])) { ?>
      url=url +"&to=<?php echo $_GET['to'];?>";
    <?php } ?>
    <?php if(isset($_GET["economico"])) { ?>
      url=url +"&economico=<?php echo $_GET['economico'];?>";
    <?php } ?>
    window.open(url);

});

$( "#excel" ).click(function() {

    var url = "admin/vehiculos/for_excel.php?excel=1"; 
    <?php if(isset($_GET["busca"])) { ?>
      url=url +"&txt_busca=<?php echo $_GET['busca'];?>";
    <?php } ?>
    <?php if(isset($_GET["rxp"])) { ?>
      url=url +"&rxp=<?php echo $_GET['rxp'];?>";
    <?php } ?>
    <?php if(isset($_GET["orden"])) { ?>
      url=url +"&orden=<?php echo $_GET['orden'];?>";
    <?php } ?>
    <?php if(isset($_GET["estatus"])) { ?>
      url=url +"&estatus=<?php echo $_GET['estatus'];?>";
    <?php } ?>
    <?php if(isset($_GET["alerta"])) { ?>
      url=url +"&alerta=<?php echo $_GET['alerta'];?>";
    <?php } ?>
    <?php if(isset($_GET["prioridad"])) { ?>
      url=url +"&prioridad=<?php echo $_GET['prioridad'];?>";
    <?php } ?>
    <?php if(isset($_GET["from"])) { ?>
      url=url +"&from=<?php echo $_GET['from'];?>";
    <?php } ?>
    <?php if(isset($_GET["to"])) { ?>
      url=url +"&to=<?php echo $_GET['to'];?>";
    <?php } ?>
    <?php if(isset($_GET["economico"])) { ?>
      url=url +"&economico=<?php echo $_GET['economico'];?>";
    <?php } ?>
    window.open(url);

});
</script>
