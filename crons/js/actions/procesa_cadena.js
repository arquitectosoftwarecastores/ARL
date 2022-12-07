'use strict';

const { promisify } = require('util');
const sleep = promisify(setTimeout);

const db = require('../db');
const HexString = require('../custom_modules/HexString.class');

module.exports = async () => {
  console.log('\nINICIANDO PROCESA CADENAS...\n');
  let arrAle = await consultarAlertas();

  let i = 0;
  do {
    let esperar = true;

    // Consulta Ultima Cadena
    const selACG = `SELECT * FROM avl_cadenas_g 
                    WHERE
                      cad_estatus = 1
                    ORDER BY cad_id desc
                    LIMIT 50`;
    console.log(`Consultando...${i++}\n`);
    const resCad = await db.qryARL(selACG);

    for (const row of resCad) {
      // Extrae Headers de Cadena
      const cadena = new HexString(row.cad_string);
      cadena.processHeaders();

      // Valida Tipo de Cadena
      if (row.cad_tipo === '02') {
        cadena.processMessageReport();
        await cadena.updateCadenasg(row.cad_id);
        await cadena.insertPosicion(row.cad_id);
        await cadena.insertParamEnganche(row.cad_id);
        await cadena.validaAlertas(arrAle);
      }
      console.log(`String: ${row.cad_string}`);
      esperar = false;
    }

    if (esperar) {
      console.log('\nNo hay cadenas que actualizar\n');

      // Actualiza Alertas
      arrAle = await consultarAlertas();
      console.log(arrAle);

      await sleep(5000).then();
    }
  } while (true);
};

async function consultarAlertas () {
  const arrAle = [];

  // Consulta Eventos y Aletas
  const selAle = `SELECT e.pk_clave_tipe, a.pk_clave_tipa 
                  FROM tb_tiposdeeventos AS e
                  INNER JOIN tb_tiposdealertas AS a
                    ON e.fk_clave_tipa = a.pk_clave_tipa`;
  const resAle = await db.qryARL(selAle);

  for (let i = 0; i < resAle.length; i++) {
    const alerta = resAle[i];
    const alev = {
      event: Number(alerta.pk_clave_tipe),
      alert: Number(alerta.pk_clave_tipa)
    };
    arrAle.push(alev);
  }

  return arrAle;
}
