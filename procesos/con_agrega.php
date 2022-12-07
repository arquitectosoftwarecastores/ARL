<?php


// Funcion alta usuario
    function altaUsuario(){
        // Consulta numero o nombre de usuario
        $seUsuario = "SELECT * FROM tb_usuarios WHERE pk_clave_usu = ?";
        $sequery = $conn->prepare($seUsuario);
        $sequery->bindParam(1, $_SESSION['id']);
        $sequery->execute();
        $seregistro = $sequery->fetch();
        $sesionusuario = $seregistro["txt_usuario_usu"];
        $sequery->closeCursor();


        $consulta  = "INSERT INTO tb_usuarios (txt_usuario_usu,txt_contrasena_usu,txt_nombre_usu,txt_email_usu,num_activo_usu,fk_clave_rol,fk_clave_emp,fecha_contrasena,fecha_mod,usuarioalta,status)
                        VALUES (?,?,?,?,?,?,?,NOW(),NOW(),?,1)";

        $query = $conn->prepare($consulta);
        $query->bindParam(1, $usuario);
        $query->bindParam(2, $contrasena);
        $query->bindParam(3, $nombre);
        $query->bindParam(4, $correo);
        $query->bindParam(5, $activo);
        $query->bindParam(6, $rol);
        $query->bindParam(7, $empresa);
        $query->bindParam(8, $sesionusuario);
        $query->execute();
        $query->closeCursor();

        $consulta1  = " SELECT * FROM  tb_usuarios WHERE txt_usuario_usu=?";
        $query1 = $conn->prepare($consulta1);
        $query1->bindParam(1, $usuario);
        $query1->execute();
        $registro1 = $query1->fetch();
        $usuario=$registro1["pk_clave_usu"];

        $circuitos=$_POST["circuitos"];
        for($i=0; $i<sizeof($circuitos); $i++)
        {
            $consulta2  = " INSERT INTO tb_circuitosxusuario
                        (fk_clave_cir,fk_clave_usu)
                        VALUES(?,?)";
            $query2 = $conn->prepare($consulta2);
            $query2->bindParam(1, $circuitos[$i]);
            $query2->bindParam(2, $usuario);
            $query2->execute();
        }

    }

    $usuario=$_POST["usuario"];
    $contrasena=$_POST["password"];
    $nombre=$_POST["nombre"];
    $correo=$_POST["correo"];
    if(isset($_POST["activo"]))
        $activo=1;
    else
        $activo=0;
    $rol=$_POST["rol"];
    $empresa=$_POST["empresa"];

    // Verifica que el usuario exista en la bd de Castores
    $servidor="192.168.0.13";
    $usuar="usuarioWin";
    $passw = "windows";
	$bd = "personal";

	try{
        // Conexion y consulta en mysql
        $var=0;
        $con = new PDO("mysql:host=".$servidor.";dbname=".$bd."",$usuar,$passw);
    	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $consult = 'SELECT COUNT(*) FROM personal WHERE idusuario = '. $usuario.' AND status = 1';
        $datos = $con->query($consult);
        foreach($datos as $row){
            echo $row[0] . '<br/>';
            $var =  $row[0];
        }

        //mero de unidades que cumplen con el criterio de busqueda son: ".$var;
        // si no encontro ningun numero coincidente en la base de datos


        // Valida si se encuentra o no el usuario
        if($var==0){
?>
            <script type="text/javascript">
                if(confirm("El usuario no existe en la base de datos de Castores, ¿Deseas agregarlo?")==true){
                    var jsVar1 = "1";
                    <?php
                    altaUsuario();
                    ?>
                    alert("El empleado se ha creado exitosamente.");

                }else{
                    var jsVar1 = "0";
                    alert("El empleado no será creado.");
                }
            </script>
<?php
            //$variablePHP = "<script>document.write(jsVar1)</script>";
            //echo "el valor de la variable actual es de: ".$variablePHP;

        }else{
            //altaUsuario();
            ?>
            <script type="text/javascript">
                alert("El empleado se ha creado exitosamente.");
            </script>
            <?php
        }

        $seccion = $_GET["seccion"];
        $redireccionar="?seccion=".$seccion."&accion=lista";
        if (isset($_GET["rxp"]))
            $redireccionar .= "&rxp=".$_GET["rxp"];

    } catch(PDOException $e) {
        // report error message
        echo $e->getMessage();
    }
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>
