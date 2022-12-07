'use strict'

const momentTimeZone = require('moment-timezone')
const localTimeZone = 'America/Mexico_City'
const db = require('../db')

module.exports = {
  /**
   * Ajusta la fecha UTC a zona horario de México
   *
   * @param {Date} utcDate Fecha con zona horario UTC
   *
   * @returns {Date}
   */
  ajustaTimeZone: (utcDate) => {
    return momentTimeZone.utc(utcDate).tz(localTimeZone)
  },

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
  obtieneIcono: (pos, dateYes) => {
    let icon = 0
    const datePos = new Date(pos.fec_ultimaposicion_pos)

    // Valda nivel de Bateria
    if (Number(pos.num_voltage_pos) <= 3400) {
      // Batetria Baja
      icon = 3
    } else if (datePos > dateYes) {
      // Posicionando
      icon = 1

      if (pos.num_event_pos === 101) {
        // Valida Si esta Hibernando
        icon = 2
      }
    }

    return icon
  },

  /**
   *
   */
  obtieneBateria: (voltage) => {
    let bateria = Number(voltage - 3400)

    if (bateria <= 0) {
      bateria = 0
    } else if (bateria >= 700) {
      bateria = 100
    } else {
      bateria = ((bateria * 100) / 700).toFixed(0)
    }

    return bateria
  },

  /**
   * Obtiene la Orientacion del GPS deacuerdo al angulo del GPS respecto al Norte
   *
   * @param {Number} dgr Valor en grados Norte como punto origen
   *
   * @returns {String}
   */
  orientacion: (dgr) => {
    let orientacion = null
    if (dgr > 337.5 || dgr <= 22.5) {
      orientacion = 'N'
    } else if (dgr > 22.5 & dgr <= 67.5) {
      orientacion = 'NE'
    } else if (dgr > 67.5 & dgr <= 112.5) {
      orientacion = 'E'
    } else if (dgr > 112.5 & dgr <= 157.5) {
      orientacion = 'SE'
    } else if (dgr > 157.5 & dgr <= 202.5) {
      orientacion = 'S'
    } else if (dgr > 202.5 & dgr <= 247.5) {
      orientacion = 'SO'
    } else if (dgr > 247.5 & dgr <= 292.5) {
      orientacion = 'O'
    } else if (dgr > 292.5 & dgr <= 337.5) {
      orientacion = 'NO'
    }
    return orientacion
  },

  updateConexiones: () => {
    const upCon = `UPDATE tb_conexionesgps
                    SET estatus = 0, fecha = NOW ()`
    db.qryARL(upCon)
  },

  updateConexion: (esn, estatus) => {
    const upCon = 'select actualiza_conexiongps($1, $2)'
    const param = [esn, estatus]
    db.qryARL(upCon, param)
  }

}
