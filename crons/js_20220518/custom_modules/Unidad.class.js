'use strict';

const db = require('../db');
const dist = require('../custom_modules/distancia');

class Unidad {
  /**
   *
   * @param {String} economico Número economico
   */
  constructor (economico) {
    this.economico = economico;
    this.coordenadas = [];
    this.rango = false;
  }

  async obtenerDetalles () {
    const posUni = `SELECT 
                      fec_ultimaposicion_pos AS fecha,
                      num_latitud_pos AS latitud,
                      num_longitud_pos AS longitud
                    FROM tb_vehiculos AS tv
                    INNER JOIN tb_posiciones AS tp
                      ON CAST(tv.num_serie_veh AS BIGINT) = tp.num_nserie_pos
                    WHERE 
                      txt_economico_veh = $1 AND 
                      status = 1 AND
                      fec_ultimaposicion_pos > (NOW())
                    ORDER BY fec_ultimaposicion_pos DESC`;
    const rowsUni = await db.qryAVL(posUni, [this.economico]);

    for (const row of rowsUni) {
      this.coordenadas.push({
        latitud: row.latitud,
        longitud: row.longitud,
        fecha: row.fecha
      });
    }
  }

  calculaDistancias () {
    for (let i = 0; i < (this.coordenadas.length - 1); i++) {
      const coord1 = this.coordenadas[i];
      const coord2 = this.coordenadas[i + 1];

      this.coordenadas[i].distancia = dist.distancia(coord1.latitud, coord1.longitud, coord2.latitud, coord2.longitud);
    }
  }

  validaRemolquesEnRango () {

  }

  /**
   *
   * @param {Object} remolque
   * @returns
   */
  remolqueEnRango (remolque) {
    this.remolque = remolque.economico;
    const coordsRem = remolque.coordenadas;

    // Recorre Array de Coordenadas de Unidad
    for (const cUni of this.coordenadas) {
    // Recorre Array de Coordenadas de Remolque
      for (const coordRem of coordsRem) {
        // Calcula distancia entre Unidad y Remolque
        const distRU = dist.distancia(coordRem.latitud, coordRem.longitud, cUni.latitud, cUni.longitud);

        // Realiza validación de distancia
        if ((distRU < cUni.distancia || distRU < 0.5) & (distRU > 0 & distRU < 40)) {
          this.rango = true;
          return;
        }
      }
    }
  }

  async insertaSQL () {
    const fecha = this.coordenadas[0] === undefined ? null : this.coordenadas[0].fecha;
    const conRem = `INSERT INTO tb_unidades_remolques (
                      unidad, remolque, valido,
                      fecha_unidad, fecha_remolque
                    ) VALUES ($1, $2, $3, $4, NOW())
                    ON CONFLICT (unidad) DO
                      UPDATE SET 
                        remolque = $2, 
                        valido = $3, 
                        fecha_unidad = $4, 
                        fecha_remolque = NOW()`;
    const inVal = [
      this.economico, this.remolque,
      Number(this.rango), fecha
    ];
    await db.qryAVL(conRem, inVal);
  }
}

module.exports = Unidad;
