'use strict';

const db = require('./db');

module.exports = {
  /**
   * Obtiene el ID de la zona en la que se encuentra el las coordenadas indicadas
   *
   * @param {Number} yLat Coordenata Latitud del punto
   * @param {Number} xLon Coordenata Longitud del punto
   * @param {Number} tipo ID del tipo de zona en el cual validar
   *
   * @returns {number}
   */
  checazona: async (yLat, xLng, tipo) => {
    let oddNodes = false;
    let arrx = [];
    let arry = [];
    let totalVertices = 0;

    const conZon = await db.qryARL('SELECT * from tb_zonas WHERE fk_clave_tipz = 1', [tipo]);
    const resZon = conZon.rows;

    for (let z = 0; z < resZon.length; z++) {
      const rowZonas = resZon[z];

      const conSeg = await db.qryARL('SELECT * FROM tb_detallezonas WHERE fk_clave_zon = 1 ORDER BY pk_clave_det ASC', [rowZonas.pk_clave_zon]);
      const resSeg = conSeg.rows;

      arrx = [];
      arry = [];
      totalVertices = 0;
      oddNodes = false;

      for (let j = 0; j < resSeg.length; j++) {
        const rowSeg = resSeg[j];
        arrx.push(rowSeg.num_longitud_zon);
        arry.push(rowSeg.num_latitud_zon);
        totalVertices++;
      }

      let j = 0;
      for (let i = 0; i < totalVertices; i++) {
        j++;
        if (j === totalVertices) {
          j = 0;
        }
        if (((arry[i] < yLat) & (arry[j] >= yLat)) || ((arry[j] < yLat) & (arry[i] >= yLat))) {
          if (arrx[i] + (yLat - arry[i]) / (arry[j] - arry[i]) * (arrx[j] - arrx[i]) < xLng) {
            oddNodes = !oddNodes;
          }
        }
      }
      if (oddNodes) {
        return rowZonas.pk_clave_zon;
      }
    }

    if (oddNodes === false) {
      oddNodes = 0;
    }
    return Number(oddNodes);
  },

  /**
   * Valida si las coordenadas se encuntra dentro o fuera de un tipos de zonas
   *
   * @param {Number} latitud Latitud de la unidad
   * @param {Number} longitud Longitud de la unidad
   * @param {Array} zonas Array con ID de zonas
   *
   * @returns {Boolean}
   */
  validaEnZonas: async (latitud, longitud, zonas) => {
    let enZona = false;

    for (let i = 0; i < zonas.length; i++) {
      const zon = zonas[i];
      const valZon = await module.exports.checazona(latitud, longitud, zon.tipo);

      if (valZon === zon.id) {
        enZona = true;
        break;
      }
    }
    return enZona;
  }
};
