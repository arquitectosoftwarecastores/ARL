'use strict'

const cron = process.argv[2]
const option = process.argv[3]

; (async => {
  const run = {
    server_tcp: require('./actions/server_tcp')(option),
    procesa_cadena: require('./actions/procesa_cadena')(),
    tb_remolques: require('./actions/tb_remolques')(),
    alertasxzonas: require('./actions/alertas_zonas/app')(),
    procesa_old: require('./actions/procesa_old')(),
    undefined: console.log('Indique un Cron a ejecutar'),
    default: console.log('No se encontro el Cron: ' + cron)
  }

  return run[cron]() || run.default
})()
