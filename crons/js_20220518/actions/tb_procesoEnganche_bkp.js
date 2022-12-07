'use strict';
const db = require('../db')
const { promisify } = require('util');
const sleep = promisify(setTimeout)
module.exports = async () => {
    do {
        console.log('\nINICIANDO CRON tb_procesoEnganche\n')
        // Obtiene tabla tb_proceso_enganche sin procesar
        let conTbProcesoEnganche = `SELECT tpe.pk_proceso_ibutton,tr.txt_economico_rem , tpe.num_ignicion_rem,
                                tpe.num_code,tpe.num_latitud_pos, tpe.num_longitud_pos
                                FROM tb_proceso_enganche tpe
                                join tb_remolques tr on tpe.txt_nserie_rem = tr.txt_nserie_rem
                                WHERE tpe.estatus = 0`;
        var resTbProcesoEnganche = await db.qryARL(conTbProcesoEnganche);
        if (resTbProcesoEnganche.length > 0) {
            for (const row of resTbProcesoEnganche) {
                var enganche = -1;
                if (row.num_ignicion_rem == 1 && row.num_code == 0) {
                    enganche = 1;
                }
                else if (row.num_ignicion_rem == 0 && row.num_code == 1) {
                    enganche = 0;
                }
                if(enganche<0){
                    continue;
                }
                var ne_rem = row.txt_economico_rem;
                // Obtiene el número económico del remolque de la tabla de enganches para determinar si se va a 
                // actualizar los datos o se van a insertar
                var conNERem_engaches = `SELECT txt_economico_rem FROM tb_enganches te WHERE txt_economico_rem = '${ne_rem}'`;
                var resNERem_engaches = await db.qryARL(conNERem_engaches);
                var fecha = Date.now();
                if (resNERem_engaches.length === 0) {
                    /// Insetar datos en tabla enganches
                    var conInsertEnganches = `INSERT INTO tb_enganches (txt_economico_rem , estatus , fecha_mod, num_latitud_pos, num_longitud_pos)
                                    VALUES ('${ne_rem}',${enganche}, ${fecha}, ${row.num_latitud_pos}, ${row.num_longitud_pos})`;
                    await db.qryARL(conInsertEnganches);
                }
                else {
                    // Obtener estatus de tabla de enganches con respecto al numero económico del remolque para determinar 
                    // se va a insertar los datos a la tabla de enganches historico
                    var conEstatusEnganche = `SELECT estatus FROM tb_enganches WHERE txt_economico_rem = '${ne_rem}' limit 1`;
                    var resEstatusEnganche = await db.qryARL(conEstatusEnganche);
                    if (resEstatusEnganche[0].estatus != enganche) {
                        // Insertar datos en tabla de enganches historico
                        var conEnganchesHist = `INSERT INTO tb_enganches_historico (txt_economico_rem , estatus , fecha_mod, num_latitud_pos, num_longitud_pos)
                                    VALUES ('${ne_rem}',${enganche}, ${fecha}, ${row.num_latitud_pos}, ${row.num_longitud_pos})`;
                        await db.qryARL(conEnganchesHist);
                    }
                    // Actualizar datos en tabla de enganches
                    var conUpEnganches = `UPDATE tb_enganches SET estatus = ${enganche}, fecha_mod = ${fecha}, num_latitud_pos = ${row.num_latitud_pos}, 
                                    num_longitud_pos = ${row.num_longitud_pos}  WHERE txt_economico_rem = '${ne_rem}'`;
                    await db.qryARL(conUpEnganches);
                }
                // Actualizar tabla de Proceso enganche 
                console.log(`cadena procesada economico = ${ne_rem}`);
                var conUpProcesoEng = `UPDATE tb_proceso_enganche SET estatus = 1  WHERE pk_proceso_ibutton = '${row.pk_proceso_ibutton}'`;
                await db.qryARL(conUpProcesoEng);
            };
            console.log("todas las cadenas fueron procesadas");
        }
        else{
            console.log("no hay cadenas para procesar");
        }
        await sleep(350000).then()
    } while (true)
};


