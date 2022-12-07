'use strict';

// Modulos
const net = require('net');
const express = require('express');
const bodyParser = require('body-parser');
const HexString = require('../custom_modules/HexString.class');
const remolque = require('../custom_modules/remolque');
const cors = require('cors');
const app = express();

const arrSockets = [];

module.exports = async (port) => {
  console.log('\nINICIANDO SERVIDOR EN PUERTO ' + port + '\n');
  // Valida PUERTO en argumento
  if (port) {
    // SERVIDOR TCP
    net.createServer((sock) => {
      serverTCP(sock);
    }).listen(port);

    // SERVIDOR HTTP
    serverHTTP(app);
    app.listen(3330);
  } else {
    console.log('Indique el Puerto como argumento\n');
  }
};

/** Funciones Para Montar Servidor TCP y HTTP */

function serverTCP (socket) {
  const address = socket.remoteAddress;
  socket.setEncoding('hex');
  let ban = true;
  let serie = '';

  /** Handler para cada la obtencion de Cadenas */
  socket.on('data', (data) => {
    // Valida Conexion
    console.log('\x1b[37m', '\n[TCP] DATOS RECIBIDOS ' + address + '\n    ' + data);
    data = data.substr(8);

    // Crea objeto y obtiene Headers
    const hexS = new HexString(data);
    hexS.processHeaders();
    // Valida nueva conexion
    if (ban) {
      console.log(' ---- NUEVA CONEXION ---- ');
      ban = false;
      serie = hexS.optionHeader.mobileID;

      addConnection();
    }

    console.log('\x1b[32m', 'ESN: ' + hexS.optionHeader.mobileID);
    const resGPS = hexS.getDefaultRes();

    switch (hexS.messageHeader.messageType) {
      case '02':
        /*
        if (hexS.optionHeader.mobileID == 4662487219) {
          console.log('**** **** **** ****')
          console.log('\x1b[31m', hexS.optionHeader.mobileID)
          console.log('**** **** **** ****')
        }
        */
        console.log('\x1b[37m', '** Event Report message **');

        // Inserta en avl_cadenas_g
        hexS.insertCadenasg(address);
        break;

      case '01':
        console.log('\x1b[34m', '** ACK/NAK message **');
        console.log('ESN: ' + serie);
        break;

      default:
        console.log('\x1b[37m', '** Unknown message **');

        break;
    }
    console.log('Respuesta a GPS: ' + resGPS);
    socket.write(resGPS, 'hex');
  });

  /** Handler para cierra de socket */
  socket.on('close', (data) => {
    console.info('\x1b[31m', '\n---- CONEXION TCP CERRADA ' + address + ' ----\n');
    closeConnection();
  });

  /** Handler para Errores */
  socket.on('error', (data) => {
    console.error('\x1b[31m', '\n<<<< <<<< <<  ERROR  >> >>>> >>>>\n');
    console.error(data);
  });

  /** Funciones */

  function addConnection () {
    // Cierra Conexiones Antiguas de la misma Serie
    closeConnection();

    arrSockets.push({
      serie: serie,
      socket: socket
    });

    remolque.updateConexion(serie, 1);
    console.log('Nueva Conexion: ' + serie);
    console.log('Conexiones Activas: ' + arrSockets.length);
  }

  function closeConnection () {
    // Elimina conexión con GPS
    for (let i = 0; i < arrSockets.length; i++) {
      const sock = arrSockets[i];
      if (sock.serie == serie) {
        sock.socket.destroy();
        arrSockets.splice(i, 1);
        ban = false;
        remolque.updateConexion(serie, 0);
        console.log('Conexion Eliminada: ' + serie);
      }
    }
    console.log('Conexiones Activas: ' + arrSockets.length);
  }
}

function serverHTTP (exp) {
  // create application/x-www-form-urlencoded parser
  exp.use(cors());
  const urlencodedParser = bodyParser.urlencoded({ extended: false });

  exp.post('/command', urlencodedParser, (req, res) => {
    let sendCmd = false;

    let resObj = {
      res: '1BAD'
    };

    const obj = JSON.parse(JSON.stringify(req.body));
    console.log('\x1b[34m', '\n[TCP] SOLICITUD DE COMANDO');
    console.log(obj);
    res.header('Access-Control-Allow-Origin', '*');

    if (obj) {
      if (obj.serie !== undefined & obj.action !== undefined &
        obj.data8 !== undefined & obj.data16 !== undefined & obj.data32 !== undefined) {
        const cmd = new HexString();
        // Realiza busqueda de socket
        for (let i = 0; i < arrSockets.length; i++) {
          const socket = arrSockets[i];
          // Valida serie
          if (socket.serie === obj.serie) {
            cmd.getCommand(socket.serie, obj.action, obj.data8, obj.data16, obj.data32);
            socket.socket.write(cmd.cmd.raw, 'hex');

            console.log('Enviando Comando a ' + obj.serie);
            console.log('Comando: ' + cmd.cmd.raw);
            sendCmd = true;
          }
        }

        // Valida el envio del comando
        if (sendCmd) {
          console.log(obj.serie);
          resObj = {
            res: '0OK',
            cmd: cmd.cmd.raw
          };
        } else {
          resObj = {
            res: 'GPS_NOT_FOUND'
          };
        }
      } else {
        resObj = {
          res: 'NO_PARAMETERS'
        };
      }
    } else {
      resObj = {
        res: 'NO_PARAMETERS'
      };
    }
    console.log(resObj);
    res.send(resObj);
    res.end();
  });
}

process.on('SIGINT', () => {
  console.log('\x1b[37m', '\n * Finalizando Cron server_tcp * \n');
  // Actualiza tabla de conexiones
  remolque.updateConexiones();
});
