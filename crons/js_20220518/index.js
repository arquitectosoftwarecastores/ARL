'use strict';

const label = require('./custom_modules/label');

const cron = process.argv[2];
const option = process.argv[3];

(async => {
  console.log(label.innovacion);

  console.log(label[cron]);

  switch (cron) {
    case 'server_tcp':
      require('./actions/server_tcp')(option);
      break;

    case 'procesa_cadena':
      require('./actions/procesa_cadena')();
      break;

    case 'tb_remolques':
      require('./actions/tb_remolques')();
      break;

    case 'tb_procesoEnganche':
      require('./actions/tb_procesoEnganche')();
      break;

    case 'alertasxzonas':
      require('./actions/alertas_zonas/app')();
      break;

    case 'procesa_old':
      require('./actions/procesa_old')();
      break;

    case 'unidad_remolque':
      require('./actions/unidad_remolque');
      break;

    case undefined:
      console.log('Indique un Cron a ejecutar');
      process.exit(0);

    default:
      console.log('No se encontro el Cron: ' + cron);
      process.exit(0);
  }
})();
