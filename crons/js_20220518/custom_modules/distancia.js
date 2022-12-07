'use strict';

const db = require('../db');

module.exports = {
  /**
 * Calcula la distancia entre dos puntos
 *
 * @param {Number} lat1 Coordenada latitud del punto 1
 * @param {Number} lon1 Coordenada longitud del punto 1
 * @param {Number} lat2 Coordenada latitud del punto 2
 * @param {Number} lon2 Coordenada longitud del punto 2
 *
 * @returns {Number}
 */
  distancia: (lat1, lon1, lat2, lon2) => {
    const EARTH_RADIUS_MI = 3963.2263272;
    const PI = Math.PI;
    let dDistMiles = 0;

    dDistMiles = Math.sin(lat1 * (PI / 180)) * Math.sin(lat2 * (PI / 180)) +
      Math.cos(lat1 * (PI / 180)) * Math.cos(lat2 * (PI / 180)) *
      Math.cos((lon1 - lon2) * (PI / 180));
    dDistMiles = EARTH_RADIUS_MI * Math.acos(dDistMiles);
    return Number(dDistMiles * 1.609344);
  },

  /**
   * Obtiene la georeferencia de las coordenadas establecidas, indicando el municipio más cercano a el
   *
   * @param {Number} lat Coordenada Latitud del punto
   * @param {Number} lon Coordenada Longitud del punto
   *
   * @returns {String}
   */
  georeferenciaMunicipio: async (lat, lon) => {
    let georeferencia = '';
    const latRango1 = lat + 0.25;
    const latRango2 = lat - 0.25;
    const lonRango1 = lon + 0.25;
    const lonRango2 = lon - 0.25;

    let colonia = '';
    let ciudad = '';
    let estado = '';
    let bandera = 0;
    let masCercano = 999999;
    let dist = 1000000;

    const conGeo = `SELECT 
                      txt_colonia_geo, num_latitud_geo,
                      num_longitud_geo, txt_cp_geo, txt_ciudad_geo,
                      txt_estadoabreviado_geo, txt_estado_geo
                    FROM tb_georeferencias 
                    WHERE 
                      num_longitud_geo >= ${lonRango2} AND
                      num_longitud_geo <= ${lonRango1} AND
                      num_latitud_geo >= ${latRango2} AND
                      num_latitud_geo <= ${latRango1}
                    ORDER BY num_longitud_geo DESC LIMIT 200`;
    const resGeo = await db.qryAVL(conGeo);

    for (let i = 0; i < resGeo.length; i++) {
      const row = resGeo[i];
      dist = module.exports.distancia(row.num_latitud_geo, row.num_longitud_geo, lat, lon);
      if (bandera === 0) {
        masCercano = dist;
        bandera = 1;
      }

      if (dist <= masCercano) {
        masCercano = dist;
        colonia = row.txt_colonia_geo;
        ciudad = row.txt_ciudad_geo;
        estado = row.txt_estado_geo;
      }
    }

    if (masCercano > 0) {
      if (colonia !== '') {
        georeferencia = `A ${masCercano.toFixed(2)} Kms de ${colonia} en ${ciudad}, ${estado}`;
      } else {
        georeferencia = 'A ' + masCercano.toFixed(2) +
          ' Kms de ' + ciudad + ', ' + estado;
      }
    } else {
      if (colonia !== '') {
        georeferencia = 'En ' + colonia + ' en ' + ciudad + ', ' + estado;
      } else {
        georeferencia = 'En ' + ciudad + ', ' + estado;
      }
    }
    return georeferencia;
  },

  /**
  * Obtiene la georeferencia de las coordenadas establecidas, indicando la sucursal Castores más cercano a el
  *
  * @param {number} lat Coordenada Latitud del punto
  * @param {number} lon Coordenada Longitud del punto
  *
  * @returns {String}
  */
  georeferenciaCastores: async (lat, lon) => {
    let georeferencia = '';
    const latRango1 = lat + 0.3;
    const latRango2 = lat - 0.3;
    const lonRango1 = lon + 0.3;
    const lonRango2 = lon - 0.3;

    let bandera = 0;
    let encontro = 0;
    let masCercano = 999999;
    let dist = 1000000;
    let pInteres = '';

    const conGeo = `SELECT * 
                    FROM tb_puntosseguros
                    WHERE
                      num_tipo_pun = 2 AND
                      num_longitud_pun >= ${lonRango2} AND
                      num_longitud_pun <= ${lonRango1} AND
                      num_latitud_pun >= ${latRango2} AND
                      num_latitud_pun <= ${latRango1}
                    ORDER BY num_longitud_pun ASC`;
    const resGeo = await db.qryAVL(conGeo);

    for (let i = 0; i < resGeo.length; i++) {
      const row = resGeo[i];
      dist = module.exports.distancia(row.num_latitud_pun, row.num_longitud_pun, lat, lon);

      if (dist <= masCercano) {
        pInteres = row.txt_nombre_pun;
        masCercano = dist;
        encontro = 1;
        bandera = 1;
      } else if (bandera === 0) {
        masCercano = dist;
        bandera = 1;
      }
    }

    if (encontro === 1) {
      if (masCercano > 0) {
        georeferencia = 'A ' + masCercano.toFixed(2) + ' Kms de ' + pInteres;
      } else {
        georeferencia = 'En ' + pInteres;
      }
    }

    return georeferencia;
  }
};
