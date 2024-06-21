<?php
class LoggerApiGoogleMaps
{
    private $connectionArl;
    private $usuario;
    private $idModulo;
    private $ubicacion;

    public function __construct($connectionArl)
    {
        $this->connectionArl = $connectionArl;
    }

    public function saveLog($usuario, $idModulo, $ubicacion)
    {
        $this->usuario = $usuario;
        $this->idModulo = $idModulo;
        $this->ubicacion = $ubicacion;
        $this->insertLog();
    }

    private function insertLog()
    {
        if (isset($this->connectionArl)) {
            $insert = $this->connectionArl->prepare("INSERT INTO monitoreo.tb_logtemporal(
                idlog, idmodulo, usuario, ubicacion, fecha)
                VALUES (default, ?, ?, ?, NOW());");
            $insert->bindParam(1, $this->idModulo);
            $insert->bindParam(2, $this->usuario);
            $insert->bindParam(3, $this->ubicacion);
            $insert->execute();
            $insert->closeCursor();
        }
    }
}
