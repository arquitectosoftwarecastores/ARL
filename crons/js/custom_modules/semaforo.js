'use strict';

const db = require('../db');

module.exports = {
  actualiza: async function (cron) {
    if (cron === undefined) {
      console.log('cron es undefined para actualizar');
      return;
    }

    const selCron = `SELECT c.id, c.tiempo_ejecucion
                      FROM tb_crones AS c
                      WHERE
                        c.nombre = $1
                      LIMIT 1`;
    const valCron = [cron];
    const rowCron = await db.qryARL(selCron, valCron);

    for (const row of rowCron) {
      const upCron = `UPDATE tb_estatus_crones
                      SET ultimo_registro = NOW()
                      WHERE id_cron = $1`;
      const res = await db.upARL(upCron, [row.id]);

      if (res.rowCount > 0) {
        const inCron = `INSERT INTO tb_estatus_crones_historico
                        (id, id_cron, fecha_registro)
                        VALUES (DEFAULT, $1, NOW())`;
        await db.qryARL(inCron, [row.id]);
        console.log(`Actualiza Semaforo de cron "${cron}"`);
        return;
      }
    }
    console.log('Cron no registrado en tb_crones');
  }
};
