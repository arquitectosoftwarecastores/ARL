'use strict'

const fnZonas = require('../../custom_modules/zonas')

// Constante de velocidad para unidad detenida
const velDet = 0.1

module.exports = {
  /**
   * Regresa el JSON con las Alertas activas
   * @param {JSON} alezon
   */
  activas: async (alezon) => {
    var newArr = []

    for (let i = 0; i < alezon.length; i++) {
      const actAle = alezon[i]
      const alerta = actAle.alerta
      console.log(' * ' + alerta.nombre)
      const Now = new Date().getHours()

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

  /**
   * Valida si una unidad pertenece a un circuito
   *
   * @param {Array} vehiculo
   * @param {Array} alerta
   *
   * @returns {Boolean}
   */
  validaCircuito: async (remCir, alerta) => {
    let resCir = false
    // Valida Circuitos
    if (alerta.circuito.length > 0) {
      for (let i = 0; i < alerta.circuito.length; i++) {
        const aleCir = Number(alerta.circuito[i])
        if (remCir === aleCir) {
          resCir = true
        }
      }
    } else {
      resCir = true
    }
    return resCir
  },

  /**
   * Valida si la unidad se encuentra detenidad
   *
   * @param {Number} velocidad
   * @param {Array} alerta
   *
   * @returns {Boolean}
   */
  validaDetenida: async (velocidad, alerta) => {
    let detenida = false
    // Valida si la alerta verifica la detencion
    if (alerta.tipo.detenida) {
      // Si es menor a la velocidad de detencion
      if (velocidad < velDet) {
        detenida = true
      }
    } else {
      detenida = true
    }
    return detenida
  },

  /**
   * Valida si las coordenadas se encuntra dentro o fuera de un tipos de zonas
   *
   * @param {Number} latitud Coordenada de Latitud
   * @param {Number} longitud Coordenada de longitud
   * @param {Array} zonas Array que contiene las zonas a validar
   *
   * @returns {Boolean}
   */
  validaEnZonas: async (latitud, longitud, zonas) => {
    var enZona = false
    for (let i = 0; i < zonas.length; i++) {
      const zon = zonas[i]
      const valZon = await fnZonas.checazona(latitud, longitud, zon.tipo)
      if (valZon === zon.id) {
        enZona = true
        break
      }
    }
    return enZona
  }
}
