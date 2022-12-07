'use strict'

const HexString = require('../../../custom_modules/HexString.class')

module.exports = serverTCP => {
  const address = serverTCP.remoteAddress
  serverTCP.setEncoding('hex')
  // let ban = Boolean(true)

  /** Handler para cada la obtencion de Cadenas */
  serverTCP.on('data', (data) => {
    console.log('\n[TCP] DATOS RECIBIDOS ' + address + '\n\t' + data)
    data = data.substr(8)

    const hexS = new HexString(data)
    hexS.processHeaders()

    let cmd = ''
    let seq = ''
    let resCad

    switch (hexS.messageHeader.messageType) {
      case '02':
        console.log('** Event Report message **')
        cmd = hexS.optionHeader.raw
        seq = hexS.messageHeader.messageSeq
        resCad = 'aa550055' + cmd + '0201' + seq + '020000000000'
        serverTCP.write(resCad, 'hex')
        console.log('Respuesta a GPS: ' + resCad)

        hexS.insertCadenasg(address)
        break

      case '01':
        console.log('** ACK/NAK message **')
        cmd = hexS.optionHeader.raw
        serverTCP.write(cmd + '02010001020000000000')
        break

      default:
        console.log('** Unknown message **')
        break
    }
    console.log(hexS)
  })

  /** Handler para cierra de socket */
  serverTCP.on('close', (data) => {
    console.info('\n---- CONEXION TCP CERRADA ' + address + ' ----\n' + data)
    // closeConection()
  })

  /** Handler para Errores */
  serverTCP.on('error', (data) => {
    console.error('\n<<<< <<<< <<  ERROR  >> >>>> >>>>\n')
    console.error(data)
    // closeConection()
  })

  /** Funciones */
  /*
  function closeConection () {
    // Elimina conexión con GPS
    for (let i = 0; i < global.arrSocket.length; i++) {
      const socket = global.arrSocket[i]
      if (serverTCP === socket.socket) {
        global.arrSocket.splice(i)
      }
    }
    console.log('Conexiones Activas: ' + global.arrSocket.length)
  }
  */
}
