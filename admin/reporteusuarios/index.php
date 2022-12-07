<?php
if (isset($_SESSION["vehiculos"])) {
    include_once("admin/reporteusuarios/def_variables.php");
    ?>
    <br>
    <h2>    Modulo de Reporte de Usuarios </h2>
    <br>
    <div class="row">   
        <div class="col-md-4">
            <form name="formulario" method="post" action="admin/reporteusuarios/reportesesiones.php"> 
                <h2> Sesiones: </h2>           
                <br/>
                Fecha Inicial: 
                <br/>
                <input type="date" name="fechainicial">
                <input type="time" name="horainicial">
                <br/>
                <br/>
                Fecha Final:    
                <br/>
                <input type="date" name="fechafinal">                    
                <input type="time" name="horafinal">
                <br/>
                <br/>
                <input type="submit" value="Generar reporte">
            </form>
        </div>
        <div class="col-md-4">
            <form name="formulario" method="post" action="admin/reporteusuarios/reportecambios.php"> 
                <h2> Modificaciones: </h2>      
                <br/>               
                Fecha Inicial: 
                <br/>
                <input type="date" name="fechainicial">
                <input type="time" name="horainicial">
                <br/>
                <br/>
                Fecha Final:    
                <br/>
                <input type="date" name="fechafinal">                    
                <input type="time" name="horafinal">
                <br/>
                <br/>
                Selecciona Modulo:  
                <br/>
                <SELECT NAME="selCombo" SIZE=1> 
                    <OPTION VALUE="1">Usuarios</OPTION> 
                    <OPTION VALUE="2">Vehículos</OPTION>
                    <OPTION VALUE="3">Zonas</OPTION>
                </SELECT> 
                <br/>
                <br/>
                <input type="submit" value="Generar reporte">
            </form>
        </div>
        <div class="col-md-4">
            <form name="formulario" method="post" action="admin/reporteusuarios/reporteconsultas.php"> 
                <h2> Consultas: </h2>
                <br/>
                                Fecha Inicial: 
                <br/>
                <input type="date" name="fechainicial">
                <input type="time" name="horainicial">
                <br/>
                <br/>
                Fecha Final:    
                <br/>
                <input type="date" name="fechafinal">                    
                <input type="time" name="horafinal">
                <br/>
                <br/>
                <input type="submit" value="Generar reporte">
            </form>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="container">
        <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Su usuario no tiene acceso a este módulo</strong>.
        </div>
    </div>
    <?php
}
?>