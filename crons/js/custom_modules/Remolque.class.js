'use strict';

const db = require('../db');
const momentTimeZone = require('moment-timezone');
const localTimeZone = 'America/Mexico_City';

class Remolque {
  /**
   * Constructor
   * @param {String} economico No economico de Remolque
   */
  constructor (economico) {
    this.economico = economico;
    this.serie = null;

    this.icono = null;

    this.latitud = null;
    this.longitud = null;

    this.geoMun = null;
    this.geoCas = null;

    this.ignicion = null;

    this.coordenadas = [];
  }

  async obtenerDetalles () {
    const detRem = `SELECT
                      tr.txt_nserie_rem AS serie,
                      tr.fec_posicion_rem AS fecha,
                      tr.num_latitud_rem AS latitud,
                      tr.num_longitud_rem AS longitud,
                      tr.num_ignicion_rem AS ignicion,
                      tr.txt_georeferencia_mun AS geomun,
                      tr.txt_georeferencia_cas AS geocas,
                      tr.num_icono_rem AS icono
                    FROM tb_remolques AS tr
                    WHERE 
                      tr.txt_economico_rem = $1 AND
                      tr.estatus = 1
                    LIMIT 1`;
    const rowsRem = await db.qryARL(detRem, [this.economico]);

    if (rowsRem.length > 0) {
      this.serie = rowsRem[0].serie;
      this.fecha = rowsRem[0].fecha;
      this.latitud = rowsRem[0].latitud;
      this.longitud = rowsRem[0].longitud;
      this.ignicion = rowsRem[0].ignicion;
      this.geoMun = rowsRem[0].geomun;
      this.geoCas = rowsRem[0].geocas;
      this.icono = rowsRem[0].icono;
    }
  }

  async obtenerUltimaPosicion () {
    const conPos = `SELECT * FROM tb_posiciones 
                      WHERE txt_nserie_pos LIKE $1 
                      ORDER BY txt_nserie_pos ASC, pk_clave_pos DESC 
                      LIMIT 1`;
    return await db.qryARL(conPos, [this.serie]);
  }

  async obtenerPosiciones () {
    const posRem = `SELECT
                        fec_ultimaposicion_pos AS fecha,
                        num_latitud_pos AS latitud,
                        num_longitud_pos AS longitud
                      FROM tb_posiciones AS tp
                      WHERE
                        tp.txt_nserie_pos = $1 AND
                        tp.fec_ultimaposicion_pos > (NOW() - INTERVAL '12 HOURS')
                      ORDER BY
                          tp.txt_nserie_pos ASC,
                          tp.fec_ultimaposicion_pos DESC`;
    const rowsPos = await db.qryARL(posRem, [this.serie]);

    for (const row of rowsPos) {
      this.coordenadas.push({
        latitud: row.latitud,
        longitud: row.longitud,
        fecha: row.fecha
      });
    }
  }

  /**
   * Ajusta la fecha UTC a zona horario de México
   *
   * @param {Date} utcDate Fecha con zona horario UTC
   *
   * @returns {Date}
   */
  ajustaTimeZone (utcDate) {
    return momentTimeZone.utc(utcDate).tz(localTimeZone);
  }

  /**
   *
   * 0 - Sin Posicionar
   * 1 - Posicionando
   * 2 - Hibernando
   * 3 - Bateria Baja a Nivel Critico
   *
   * @param {Array} pos Registros de la posicon a validar
   *
   * @returns {Integer}
   */
  obtieneIcono (pos, dateYes) {
    let icon = 0;
    const datePos = new Date(pos.fec_ultimaposicion_pos);

    // Valda nivel de Bateria
    if (Number(pos.num_voltage_pos) <= 3500) {
      // Batetria Baja
      return 3;
    }
    if (datePos > dateYes) {
      // Posicionando
      icon = 1;

      if (pos.num_event_pos === 101) {
        // Valida Si esta Hibernando
        icon = 2;
      }
    }

    return icon;
  }

  /**
   *
   */
  obtieneBateria (voltage) {
    let bateria = Number(voltage - 3450);

    if (bateria <= 0) {
      bateria = 0;
    } else if (bateria >= 700) {
      bateria = 100;
    } else {
      bateria = ((bateria * 100) / 700).toFixed(0);
    }

    return bateria;
  }

  /**
   * Obtiene la Orientacion del GPS deacuerdo al angulo del GPS respecto al Norte
   *
   * @param {Number} dgr Valor en grados Norte como punto origen
   *
   * @returns {String}
   */
  orientacion (dgr) {
    let orientacion = null;
    if (dgr > 337.5 || dgr <= 22.5) {
      orientacion = 'N';
    } else if (dgr > 22.5 & dgr <= 67.5) {
      orientacion = 'NE';
    } else if (dgr > 67.5 & dgr <= 112.5) {
      orientacion = 'E';
    } else if (dgr > 112.5 & dgr <= 157.5) {
      orientacion = 'SE';
    } else if (dgr > 157.5 & dgr <= 202.5) {
      orientacion = 'S';
    } else if (dgr > 202.5 & dgr <= 247.5) {
      orientacion = 'SO';
    } else if (dgr > 247.5 & dgr <= 292.5) {
      orientacion = 'O';
    } else if (dgr > 292.5 & dgr <= 337.5) {
      orientacion = 'NO';
    }
    return orientacion;
  }

  updateConexiones () {
    const upCon = `UPDATE tb_conexionesgps
                    SET estatus = 0, fecha = NOW ()`;
    db.qryARL(upCon);
  }

  updateConexion (esn, estatus) {
    const upCon = 'select actualiza_conexiongps($1, $2)';
    const param = [esn, estatus];
    db.qryARL(upCon, param);
  }

  /**
   * Metodo para generar alertas
   * Valida
   *
   * @param {Number} tipoAlerta
   * @param {Number} prioridad
   * @param {Number} tiempo
   * @param {Array|undefined} descripcion
   */
  async generarAlerta (tipoAlerta, prioridad, tiempo, descripcion) {
    // Valida exitencia de la variable tiempo
    if (Number(tiempo) <= 0) {
      tiempo = 30;
    }

    if (Number(prioridad) <= 0) {
      prioridad = 3;
    }

    // Consulta número de Alertas en los ultimos 30 minutos
    const conNuAl = `SELECT COUNT(*) AS numale  
                      FROM monitoreo.tb_alertas
                      WHERE 
                        fec_fecha_ale >  (NOW() - INTERVAL '${tiempo} MINUTE') AND 
                        fk_clave_tipa = $1 AND 
                        txt_economico_rem = $2;`;
    const valNuAl = [tipoAlerta, this.economico];
    const res = await db.qryARL(conNuAl, valNuAl);
    const numAle = Number(res[0].numale);

    if (numAle === 0) {
      // Inserta Alerta

      let valAle = [
        tipoAlerta,
        this.economico,
        this.latitud,
        this.longitud,
        this.ignicion,
        prioridad,
        this.geoMun,
        this.geoCas
      ];

      let qryDesc = '';
      let valDesc = '';

      // Valida existencia de Array
      if (Array.isArray(descripcion)) {
        for (let i = 0; i < descripcion.length; i++) {
          qryDesc += `,txt_campo${1 + i}_ale`;
          valDesc += `,$${9 + i}`;
        }
        valAle = valAle.concat(descripcion);
      }

      const inAle = `INSERT INTO tb_alertas
                        (
                          fk_clave_tipa,
                          txt_economico_rem, fec_fecha_ale,
                          num_latitud_ale, num_longitud_ale,
                          txt_ignicion_ale, num_prioridad_ale,
                          txt_upsmart_ale, txt_ubicacion_ale,
                          num_tipo_ale ${qryDesc}
                        )
                      VALUES ($1, $2, NOW(), $3, $4, $5, $6, $7, $8, 0 ${valDesc})`;

      // Realiza Insercion de nueva alerta
      console.log(' Alerta Generada:', tipoAlerta, '-', this.economico);
      await db.qryARL(inAle, valAle);
    }
  }
}

module.exports = Remolque;
