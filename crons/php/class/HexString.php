<?php

class HexString {
  // Variables
  public $tipo;
  public $nserie;
  public $string;
  public $cad_estatus;

  // Variables de Posicion
  public $fecPosicion;
  public $latitud;
  public $longitud;
  public $ignicion;
  public $velocidad;


  # Constructor
  function __construct ($res) {
    $this->tipo = $res['cad_tipo'];
    $this->nserie = $res['cad_nserie'];
    $this->string = $res['cad_string'];
    

  }

  # Metodos
}
