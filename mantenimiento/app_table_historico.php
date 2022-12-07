<style>
  #customers {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    margin: 0px;
    font-size: 14px;
  }

  #customers td,
  #customers th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #customers tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  #customers tr:hover {
    background-color: #ddd;
  }

  #customers th {
    padding: 5px;
    background-color: #4CAF50;
    color: white;
  }

  .centrado {
    text-align: center !important;
  }
</style>

<table id="customers">
  <thead class="centrado">
    <tr>
      <th>Fecha Mantenimiento</th>
      <th>Usuario</th>
      <th>Observación</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Valida obtension de Economico
    if (isset($_GET["economico"])) {
      include('../conexion/conexion.php');
      $economico = $_GET["economico"];

      // Consulta Historico de Posiciones
      $consultaalertas = 'SELECT * 
                          FROM tb_mantenimientos_historico AS tbh
                          LEFT JOIN tb_usuarios AS tu
                            ON tbh.usuarios = tu.txt_usuario_usu
                          WHERE economico_rem = ? 
                          ORDER BY pk_mantenimiento_his DESC;';
      $qryMnt = $conn->prepare($consultaalertas);
      $qryMnt->bindParam(1, $economico);
      $qryMnt->execute();

      while ($regMnt = $qryMnt->fetch()) {
        // Genera Cuerpo de Tabla
        $fecha = substr($regMnt["fecha_mtto"], 0, 16);
        $usuario = $regMnt["txt_nombre_usu"];
        $observacion = $regMnt["observacion"];
    ?>
        <tr>
          <td><?php echo $fecha; ?></td>
          <td><?php echo $usuario; ?></td>
          <td><?php echo $observacion; ?></td>
        </tr>
    <?php
      }
      $qryMnt->closeCursor();
    }
    ?>
  </tbody>
</table>