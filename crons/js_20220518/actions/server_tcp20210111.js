'use strict'

// Modulos
const net = require('net')
const express = require('express')
const bodyParser = require('body-parser')
const HexString = require('../custom_modules/HexString.class')
const app = express()
/*
app.use(bodyParser.json())
app.use(bodyParser.urlencoded({ extended: true }))
*/
let arrSockets = []

module.exports = async (port) => {
  console.log('\nINICIANDO SERVIDOR EN PUERTO ' + port + '\n')
  // Valida PUERTO en argumento
  if (port) {
    // SERVIDOR TCP
    const serv = await net.createServer(serverTCP)
    serv.listen(port)
    const address = serv.address()
    console.log(`SERVIDOR TCP ESCUCHANDO EN ${address.address}:${address.port}\n`)

    // SERVIDOR HTTP
    serverHTTP(app)
    app.listen(3330)
  } else {
    console.log('Indique el Puerto como argumento\n')
  }
}

/** Funciones Para Montar Servidor TCP y HTTP */

function serverTCP (socket) {
  const address = socket.remoteAddress
  socket.setEncoding('hex')
  let ban = true
  let serie = ''

  /** Handler para cada la obtencion de Cadenas */
  socket.on('data', (data) => {
    // Valida Conexion
    console.log('\x1b[37m', '\n[TCP] DATOS RECIBIDOS ' + address + '\n\t' + data)
    data = data.substr(8)

    // Crea objeto y obtiene Headers
    const hexS = new HexString(data)
    hexS.processHeaders()
    // Valida nueva conexion
    if (ban) {
      console.log(' ---- NUEVA CONEXION ---- ')
      ban = false
      serie = hexS.optionHeader.mobileID

      addConnection()
    }

    console.log(serie + ' - ' + hexS.optionHeader.mobileID)
    console.log('\x1b[32m', 'ESN: ' + hexS.optionHeader.mobileID)
    let resGPS

    switch (hexS.messageHeader.messageType) {
      case '02':
        /*
        if (hexS.optionHeader.mobileID == 4662489046) {
          console.log('**********************************************************')
          console.log(hexS.optionHeader.mobileID)
        }
        */
        console.log('\x1b[37m', '** Event Report message **')
        resGPS = hexS.getDefaultRes()
        console.log('Respuesta a GPS: ' + resGPS)
        socket.write(resGPS, 'hex')
        // Inserta en avl_cadenas_g
        hexS.insertCadenasg(address)
        break

      case '01':
        console.log('\x1b[34m', '** ACK/NAK message **')
        resGPS = hexS.getDefaultRes()
        socket.write(resGPS, 'hex')
        console.log('ESN: ' + serie)
        break

      default:
        console.log('\x1b[37m', '** Unknown message **')
        break
    }
  })

  /** Handler para cierra de socket */
  socket.on('close', (data) => {
    console.info('\x1b[31m', '\n---- CONEXION TCP CERRADA ' + address + ' ----\n')
    closeConnection()
  })

  /** Handler para Errores */
  socket.on('error', (data) => {
    console.error('\x1b[31m', '\n<<<< <<<< <<  ERROR  >> >>>> >>>>\n')
    console.error(data)
  })

  /** Funciones */

  function addConnection () {
    // Cierra Conexiones Antiguas de la misma Serie
    closeConnection()

    arrSockets.push({
      serie: serie,
      socket: socket
    })
    console.log('Nueva Conexion: ' + serie)
    console.log('Conexiones Activas: ' + arrSockets.length)
  }

  function closeConnection () {
    // Elimina conexión con GPS
    for (let i = 0; i < arrSockets.length; i++) {
      const sock = arrSockets[i]
      if (sock.serie == serie) {
        arrSockets.splice(i, 1)
        console.log('Conexion Eliminada: ' + serie)
      }
    }
    console.log('Conexiones Activas: ' + arrSockets.length)
  }
}

function serverHTTP (exp) {
  // create application/x-www-form-urlencoded parser
  const urlencodedParser = bodyParser.urlencoded({ extended: false })

  exp.post('/command', urlencodedParser, (req, res) => {
    let sendCmd = false

    const obj = JSON.parse(JSON.stringify(req.body))
    console.log('\x1b[34m', '\n[TCP] SOLICITUD DE COMANDO')
    console.log(obj)

    if (obj) {
      if (obj.serie !== undefined & obj.action !== undefined &
        obj.peg !== undefined & obj.event !== undefined) {
        const cmd = new HexString()
        // Realiza busqueda de socket
        for (let i = 0; i < arrSockets.length; i++) {
          const socket = arrSockets[i]
          // Valida serie
          if (socket.serie === obj.serie) {
            cmd.getCommand('4662487219')
            socket.socket.write(cmd.cmd.raw, 'hex')

            console.log('Enviando Comando a ' + obj.serie)
            console.log('Comando: ' + cmd.cmd.raw)
            sendCmd = true
          }
        }

        // Valida el envio del comando
        if (sendCmd) {
          console.log(obj.serie)
          const resObj = {
            cmd: cmd.cmd.raw
          }
          res.send(resObj)
        } else {
          const defRes = {
            res: 'GPS_NOT_FOUND'
          }
          console.log(defRes)
          res.send(defRes)
        }
      } else {
        console.log('NO PARAMETERS')
      }
    } else {
      console.log(req.body)
      res.send('NO PARAMETERS')
    }
    res.end()
  })
}
