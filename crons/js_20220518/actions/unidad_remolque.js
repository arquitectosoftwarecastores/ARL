/** @module unidad_remolque */

'use strict';

const db = require('../db');
const Unidad = require('../custom_modules/Unidad.class');
const Remolque = require('../custom_modules/Remolque.class');

(async () => {
  do {
    // Consulta Oficinas Replicadas
    const repOfi = [];
    const conOfi = `SELECT idoficina_replica
                    FROM talones.viajes_remolque_hist
                    GROUP BY idoficina_replica`;
    const rowOfi = await db.qry23(conOfi, []);
    for (const ofi of rowOfi[0]) {
      repOfi.push(ofi.idoficina_replica);
    }

    // Consulta Bitacora de Unidades
    const conUR = `SELECT
                    c.noeconomico AS unidad,
                    r.noeconomico AS remolque,
                    b.idviaje, b.idoficinaviaje,
                    o.plaza AS oficina
                  FROM camiones.bitacora b
                  INNER JOIN camiones.camiones c
                    ON b.idunidad = c.unidad
                  INNER JOIN camiones.remolques r
                    ON b.idremolque = r.idremolque
                  INNER JOIN castores.oficinas AS o
                    ON b.idoficinaviaje = o.idoficina
                  WHERE
                    b.estatusviaje IN (1, 2) AND
                    b.idremolque IS NOT NULL AND
                    b.idoperador > 0 AND
                    c.idtipounidad = 1 AND
                    c.status = 1 AND
                    r.status = 1
                  ORDER BY b.idunidad `;
    const rowsUR = await db.qry23(conUR, []);

    for (const rowUR of rowsUR[0]) {
      console.log('*******************************************');
      console.log('Unidad:', rowUR.unidad);
      console.log('Remolque:', rowUR.remolque);

      // Genrea Objetos de Unidad
      const unidad = new Unidad(rowUR.unidad);
      await unidad.obtenerDetalles();

      let idalerta = null;
      const remViaje = new Remolque(rowUR.remolque);
      await remViaje.obtenerDetalles();

      // Valida Existencia de Coiirdenadas de la Unidad
      if (unidad.coordenadas.length > 0) {
        //
        let remolque = remViaje;

        // Consulta Historico de Vínculo de Remolques
        const conRem = `SELECT 
                          vrh.idremolque, 
                          r.noeconomico AS remolque,
                          idoficina_replica
                        FROM talones.viajes_remolque_hist AS vrh
                        INNER JOIN camiones.remolques AS r
                          ON vrh.idremolque = r.idremolque
                        WHERE
                          idviaje = ? AND idoficina = ?
                        ORDER BY 
                          idviaje, idoficina,
                          vrh.fechamod DESC, vrh.horamod DESC
                          LIMIT 1`;
        const resRem = await db.qry23(conRem, [rowUR.idviaje, rowUR.idoficinaviaje]);

        if (resRem[0].length > 0) {
          const findOfi = repOfi.find(x => x === resRem[0].idoficina_replica);

          // Valida si NO es de la oficina
          if (!findOfi) {
            continue;
          }

          // Crea Objeto con Remolque de Historico
          remolque = new Remolque(resRem[0].remolque);
          await remolque.obtenerDetalles();
        }

        if (resRem[0].length === 0) {
          const findOfi = repOfi.find(x => x === rowUR.idoficinaviaje);

          // Valida si NO esta en la oficina
          if (!findOfi) {
            continue;
          }
        }

        // Obtiene informacion de Remolque
        await remolque.obtenerPosiciones();

        if (remolque.coordenadas.length > 0) {
          unidad.calculaDistancias();
          unidad.remolqueEnRango(remolque);

          // Valida si la Unidad esta fuera de Rango
          if (!unidad.rango) {
            // * Remolque Fuera de Rango *
            idalerta = 211;
          }
        } else {
          // * Remolque Sin Reportar *
          idalerta = 212;
          continue;
        }
      } else {
      // * Unidad Sin Reportar *
        idalerta = 213;
      }

      if (idalerta !== null) {
        const conFol = `SELECT folio 
                        FROM talones.viajes 
                        WHERE idviaje = ? and idoficina = ?
                        LIMIT 1`;
        const rowFolio = await db.qry23(conFol, [rowUR.idviaje, rowUR.idoficinaviaje]);

        let folio = '';
        if (rowFolio[0][0]) {
          folio = rowFolio[0][0].folio;
        }

        const tiempo = 1440;
        const descripcion = [
          unidad.economico,
          folio,
          rowUR.idviaje,
          rowUR.oficina,
          rowUR.idoficinaviaje
        ];

        // * Genera Alerta *
        console.log('Alerta:', idalerta);
        await remViaje.generarAlerta(idalerta, 3, tiempo, descripcion);
      }
    }
    console.log('FIN CRON');
    await new Promise(resolve => setTimeout(resolve, 600000));
  } while (true);
})();
