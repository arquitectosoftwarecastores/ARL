const db = require('../db')
const HexString = require('../custom_modules/HexString.class')

module.exports = async () => {
  console.log('Iniciando Proceso')
  const selRem = `SELECT * 
                  FROM tb_remolques
                  ORDER by txt_nserie_rem ASC`
  const resRem = await db.qryARL(selRem)

  console.log('Procesando...')

  for (let i = 0; i < resRem.length; i++) {
    const rem = resRem[i]

    console.log(rem.txt_economico_rem)

    const selRem = `SELECT * from tb_posiciones
                    where 
                      txt_nserie_pos = '${rem.txt_nserie_rem}' AND 
                      num_voltage_pos is null
                    order by txt_nserie_pos ASC, pk_clave_pos desc
                    LIMIT 100`
    const resPos = await db.qryARL(selRem)

    for (let i = 0; i < resPos.length; i++) {
      const pos = resPos[i]
      const cad = new HexString(pos.txt_cadena_pos)

      cad.processHeaders()
      await cad.processMessageReport()

      const upCad = `UPDATE tb_posiciones
                      SET 
                        num_charge_pos = $1, 
                        num_voltage_pos = $2,
                        txt_idbutton_pos = $3,
                        num_event_pos = $4,
                        num_satellites_pos = $5,
                        txt_fixstatus_pos = $6,
                        num_carrier_pos = $7,
                        num_rssi_pos = $8,
                        txt_commstate_pos = $9,
                        txt_unitstatus_pos = $10,
                        num_motion_pos = $11,
                        num_powerstate_pos = $12,
                        txt_accum3_pos = $13,
                        num_ignicion_pos = $14
                      WHERE pk_clave_pos = $15`
      const valUp = [
        cad.message.charge,
        cad.message.voltage,
        cad.message.idButton,
        cad.message.eventCode,
        cad.message.satellites,
        cad.message.fixStatus,
        cad.message.carrier,
        cad.message.rssi,
        cad.message.commState,
        cad.message.unitStatus,
        cad.message.motion,
        cad.message.powerState,
        cad.message.accum3,
        cad.message.ignition,
        pos.pk_clave_pos
      ]
      db.qryARL(upCad, valUp)
    }
  }
  console.log('Fin')
}
