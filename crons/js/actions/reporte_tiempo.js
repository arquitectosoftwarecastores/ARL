'use strict';

const db = require('../db');

(async () => {
  // Consulta Remolques
  const conRem = `SELECT *
                  FROM tb_remolques tr
                  WHERE estatus = 1`;
  const rowRem = db.qryARL(conRem, []);

  for (const rem of rowRem) {
    console.log(rem);
  }
})();
