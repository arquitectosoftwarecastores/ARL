<div class="container mt-4">

  <form id="formComandos" onsubmit="actionSubmitCMD();return false">
    <div class="form-row">
      <div class="col-sm">
        <label for="selEconomico">No Economico</label>
        <select name="selEconomico" id="selEconomico" class="form-control" onchange="addEconomico()">
          <option value="">-</option>
          <?php
          $selEco = 'SELECT * FROM tb_remolques 
                      WHERE estatus = 1 
                      ORDER BY txt_economico_rem ASC';
          $qryEco = $conn->prepare($selEco);
          $qryEco->execute();
          while ($resEco = $qryEco->fetch()) {
          ?>

            <option value="<?php echo $resEco['txt_nserie_rem'] ?>">
              <?php echo $resEco['txt_economico_rem'] ?>
            </option>

          <?php
          }
          ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="col-lg">
        <hr>
      </div>
    </div>

    <div class="form-row">
      <div class="col-lg centrado" id="listEconomico">
      </div>
    </div>


    <div class="form-row">
      <div class="col-lg">
        <hr>
      </div>
    </div>

    <div class="form-row">
      <div class="col-sm">
        <label for="txtData8">Action</label>
        <input type="text" class="form-control" name="txtSAction" id="txtAction" value="0" maxlength="3" onkeyup="generaComando()" required>
      </div>

      <div class="col-sm">
        <label for="txtData8">Data 8</label>
        <input type="text" class="form-control" name="txtData8" id="txtData8" value="0" maxlength="3" onkeyup="generaComando()" required>
      </div>

      <div class="col-sm">
        <label for="txtData16">Data 16</label>
        <input type="text" class="form-control" name="txtData16" id="txtData16" value="0" maxlength="5" onkeyup="generaComando()" required>
      </div>

      <div class="col-sm">
        <label for="txtData32">Data 32</label>
        <input type="text" class="form-control" name="txtData32" id="txtData32" value="0" maxlength="10" onkeyup="generaComando()" required>
      </div>

    </div>

    <div class="form-row">
      <div class="col-sm">
        <label for="txtCMD">Comando</label>
        <input type="text" name="txtCMD" id="txtCMD" class="form-control" value=" 80 01 07 00 00 03 00 00 00 00 00 00 00" required readonly>
      </div>
    </div>

    <div class="form-row mt-4">
      <div class="col-sm">
        <textarea name="txtConsole" id="txtConsole" class="form-control" style="background-color: #191919; color: white; height: 240px; font-size: 12px; font-weight: bold" readonly></textarea>
      </div>
    </div>

    <div class="form-row">
      <div class="col-sm">
        <div class="progress">
          <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="1000" aria-valuemin="0" aria-valuemax="100" id="progress"></div>
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="col-lg derecha">
        <hr>
        <input type="reset" value="LIMPIAR" onclick="cleanFormCMD()" class="btn btn-info" id="btnLimpiarForm">
        <input type="submit" value="ENVIAR" class="btn btn-success" id="btnSubmitCmd">
      </div>
    </div>

  </form>

</div>

<script src="tools/con_comandos.js"></script>