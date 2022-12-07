'use strict'

const db = require('../db')
const jsonAleZon = require('../config/alertas_zonas.json')

module.exports = {
  /**
   * Consulta las alertas activade del JSON alerta_zonas.js
   * y regresa array con alertas activas
   *
   * @returns {Array}
   */
  getAlertaxZonas: () => {
    let newArr = []

    for (let i = 0; i < jsonAleZon.length; i++) {
      const actAle = jsonAleZon[i]
      const alerta = actAle.alerta
      const Now = new Date().getHours()
      console.log(' * ' + alerta.nombre)

      if (alerta.estatus === 1) {
        // Valida Horario
        if ((alerta.horario.inicio === undefined) ||
          (Now >= alerta.horario.inicio || Now < alerta.horario.fin)) {
          console.log('\tEn tiempo')
          newArr.push(actAle)
        } else {
          console.log('\tAlerta Fuera de Tiempo')
        }
      } else {
        console.log('\tAlerta Deshabilitada')
      }
    }
    return newArr
  },
  validaCharge: (charge) => {
    if (charge < 3700) {
      this.generar()
    }
  },
  /**
   * Valida si una alerta fue generada hace 30 minutos y genera nueva Alerta
   *
   * @param {Array} unidad
   * @param {Number} tipoAlerta
   */
  generar: async (economico, lat, lon, geoMun, geoCas, ign, tipoAle) => {
    // Consulta número de Alertas en los ultimos 30 minutos
    const conNuAl = `SELECT COUNT(*) AS numale  
                      FROM monitoreo.tb_alertas
                      WHERE 
                        fec_fecha_ale > NOW() - INTERVAL '30 MINUTE' AND 
                        fk_clave_tipa = $1 AND 
                        txt_economico_veh = $2;`
    const valNuAl = [tipoAle, economico]
    const res = await db.qryARL(conNuAl, valNuAl)
    const numAle = Number(res.rows[0].numale)
    if (numAle === 0) {
      // Realiza Insercion de nueva alerta
      const inAle = `INSERT INTO tb_alertas 
                        (fk_clave_tipa, fec_fecha_ale, txt_ubicacion_ale, 
                        txt_economico_veh, txt_ignicion_ale, 
                        num_prioridad_ale, num_latitud_ale, 
                        num_longitud_ale, txt_upsmart_ale, num_tipo_ale) 
                      VALUES ($1, NOW(), $2, $3, $4, 3, $5, $6, $7, 0)`
      const valAle = [
        tipoAle,
        economico,
        lat,
        lon,
        ign,
        geoMun,
        geoCas
      ]
      db.qryARL(inAle, valAle)
      //  console.log(' Alerta Generada: ' + tipoAle + ' - ' + economico)
    }
  }
}
