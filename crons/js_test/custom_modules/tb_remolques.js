'use strict'

const db = require('../db')
const fnDist = require('../custom_modules/distancia')
const fnG = require('../custom_modules/remolque')
const { promisify } = require('util')
const sleep = promisify(setTimeout)

let dateYes

module.exports = async () => {
  do {
    console.log('\nINICIANDO CRON tb_remolques\n')

    dateYes = new Date()
    dateYes = dateYes.setDate(dateYes.getDate() - 1)
    dateYes = new Date(dateYes)

    // Obtiene Lista de Unidades
    const conRem = `SELECT * 
                    FROM tb_remolques 
                    WHERE estatus = 1 
                    ORDER BY txt_nserie_rem ASC`
    const resRem = await db.qryARL(conRem)

    for (let i = 0; i < resRem.length; i++) {
      const remolque = resRem[i]

      console.log(remolque.txt_economico_rem)
      // Obtiene ultima posicion
      const conPos = `SELECT * FROM tb_posiciones 
                      WHERE txt_nserie_pos LIKE $1 
                      ORDER BY txt_nserie_pos ASC, pk_clave_pos DESC 
                      LIMIT 1`
      let resPos = await db.qryARL(conPos, [remolque.txt_nserie_rem])
      resPos = resPos[0]

      if (resPos !== undefined) {
        // Valida que la coordenada no se encuentre en 0
        let upCoord = ''
        if (resPos.num_longitud_pos !== 0) {
          upCoord = `num_latitud_rem = ${resPos.num_latitud_pos}, 
                      num_longitud_rem = ${resPos.num_longitud_pos}, 
                      num_altitud_rem = ${resPos.num_altitud_pos},
                      txt_orientacion_rem = '${fnG.orientacion(resPos.num_orientacion_pos)}', 
                      num_velocidad_rem = ${resPos.num_velocidad_pos}, 
                      txt_georeferencia_mun = '${await fnDist.georeferenciaMunicipio(resPos.num_latitud_pos, resPos.num_longitud_pos)}', 
                      txt_georeferencia_cas = '${await fnDist.georeferenciaCastores(resPos.num_latitud_pos, resPos.num_longitud_pos)}', `
        }

        // Actualiza Informacion de Remolques
        const upRem = `UPDATE tb_remolques 
                        SET 
                          ${upCoord}
                          fec_posicion_rem = $1, 
                          num_ignicion_rem = $2, 
                          txt_inputs_rem = $3, 
                          num_icono_rem = $4,
                          num_bateria_rem = $5
                        WHERE txt_nserie_rem = $6`
        const parmRem = [
          resPos.fec_ultimaposicion_pos,
          resPos.num_ignicion_pos,
          resPos.txt_inputs_pos,
          fnG.obtieneIcono(resPos, dateYes),
          fnG.obtieneBateria(resPos.num_voltage_pos),
          remolque.txt_nserie_rem
        ]
        db.qryARL(upRem, parmRem)
      } else {
        console.log('Sin Cadenas ' + remolque.txt_nserie_rem)
      }
    }
    console.log('-----')
    await sleep(350000).then()
  } while (true)
}
