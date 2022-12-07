'use strict'

// Modulos
const net = require('net')

module.exports = async (port) => {
  console.log('\nINICIANDO SERVIDOR EN PUERTO ' + port + '\n')
  // Valida PUERTO en argumento
  if (port) {
    // Inicializa SERVIDOR TCP
    const serverTCP = require('./routes/serverTCP')
    const serv = await net.createServer(serverTCP)
    serv.listen(3300)
    const address = serv.address()
    console.log(`SERVIDOR TCP ESCUCHANDO EN ${address.address}:${address.port}\n`)
  } else {
    console.log('Indique el Puerto como argumento\n')
  }
}
