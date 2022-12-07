'use strict'

// Modulos
const db = require('../../db')
const jsonAleZon = require('../../config/alertas_zonas.json')
const fnAleZon = require('./fnAleZon')
const fnAlertas = require('../../custom_modules/alertas')

module.exports = async () => {
  do {
    // Obtiene las Alertas Activas
    const arrAleZon = await fnAleZon.activas(jsonAleZon)
    await sleep(1000)

    // Valida el Numero de alertas Activas
    if (arrAleZon.length > 0) {
      // Consulta Vehiculos
      console.log('Consultando Vehiculos...')
      const conRem = 'SELECT * ' +
                      'FROM monitoreo.tb_remolques ' +
                      'WHERE ' +
                        'estatus = 1 ' +
                      'ORDER BY txt_economico_rem;'
      const x = await db.qryARL(conRem)
      const remolques = x.rows

      for (let j = 0; j < remolques.length; j++) {
        const remolque = remolques[j]
        const eco = remolque.txt_economico_rem
        const cir = Number(remolque.fk_clave_cir)
        const lat = remolque.num_latitud_rem
        const lon = remolque.num_longitud_rem
        const geoMun = remolque.txt_georeferencia_mun
        const geoCas = remolque.txt_georeferencia_cas
        const ign = remolque.num_ignicion_rem
        const vel = Number(remolque.num_velocidad_rem)
        console.log('\nEconomico: ' + eco)
        console.log('Circuito: ' + cir)

        // Valida Alertas
        for (let i = 0; i < arrAleZon.length; i++) {
          const axz = arrAleZon[i]
          const alerta = axz.alerta
          const zona = axz.zona

          // Valida si el circuito de la Unidad pertenece a la Alerta
          const enCircuito = await fnAleZon.validaCircuito(cir, alerta)
          if (enCircuito) {
            console.log('Alerta: ' + alerta.nombre)
            // Realiza Validacion de Unidad Detenida
            const unidadDetenida = await fnAleZon.validaDetenida(vel, alerta)
            if (unidadDetenida) {
              const enZona = await fnAleZon.validaEnZonas(lat, lon, zona)
              if (alerta.tipo.enZona === enZona) {
                fnAlertas.generar(eco, lat, lon, geoMun, geoCas, ign, alerta.id)
              }
            }
          }
        }
      }
      await sleep(4000)
    } else {
      console.log('\nSin Alertas Activas\n')
      await sleep(60000)
    }
  } while (true)
}

// Funciones para
async function sleep (time) {
  console.log('\nDescanso... ' + time + 'ms\n')
  await sleepMs(time)
}

function sleepMs (ms) {
  return new Promise(resolve => setTimeout(resolve, ms))
}
