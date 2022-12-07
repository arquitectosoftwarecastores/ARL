'use strict'

/** */
module.exports = {
  /**
   * Convierte el valor Hexadecimal a Decimal
   *
   * @param {string} hex Valor Hexadecimal
   *
   * @returns number
   */
  toDecimal: function (hex) {
    let dec = parseInt(hex, 16)
    // Realiza conversion de Hexadecimal a Decimas

    if (hex.length === 2 && dec >= parseInt('80', 16)) {
      dec = dec - parseInt('100', 16)
    }
    if (hex.length === 4 && dec >= parseInt('8000', 16)) {
      dec = dec - parseInt('10000', 16)
    }
    if (hex.length === 8 && dec >= parseInt('80000000', 16)) {
      dec = dec - parseInt('100000000', 16)
    }

    const t1 = parseInt('8000000000000000', 16)
    const t2 = parseInt('10000000000000000', 16)

    if (hex.length === 16 && dec >= t1) {
      dec = dec - t2
    }

    if (hex.length === 2 || hex.length === 4 || hex.length === 8 || hex.length === 16) {
      // dec = dec.toString(10)
    } else {
      dec = dec.toString(10)
    }

    return Number(dec)
  },

  /**
   * Convierte un número Hexadecimal a IP
   *
   * @param {string} hex Parametro a convertir a IP
   *
   * @returns string
   */
  toIp: function (hex) {
    hex.replace(/\r\n/g, '\n')
    const lines = hex.split('\n')
    let output = ''

    for (let i = 0; i < lines.length; i++) {
      let line = lines[i]
      line = line.replace(/0x/gi, '')
      let match
      let matchText

      if (line.indexOf('.') > 0) {
        match = /([0-f]+\.[0-f]+\.[0-f]+\.[0-f]+)/i.exec(line)

        if (match) {
          matchText = match[1]
          const ipParts = matchText.split('.')
          const p0 = parseInt(ipParts[0], 16)
          const p1 = parseInt(ipParts[1], 16)
          const p2 = parseInt(ipParts[2], 16)
          const p3 = parseInt(ipParts[3], 16)
          output += p0 + '.' + p1 + '.' + p2 + '.' + p3
        } else {
          output += line
        }
      } else {
        match = /([0-f]+)/i.exec(line)
        if (match) {
          matchText = parseInt(match[1], 16)
          const converted = ((matchText >> 24) & 0xff) + '.' +
            ((matchText >> 16) & 0xff) + '.' +
            ((matchText >> 8) & 0xff) + '.' +
            (matchText & 0xff)
          output += converted
        } else {
          output += line
        }
      }
      output += ''
    }

    return output
  },

  /**
   * Convierte valor Hexadecimal a Fecha
   *
   * @param {string} hex Parametro hexadecimal a convertir
   *
   * @returns Date
   */
  toDate: function (hex) {
    const dec = parseInt(hex, 16)
    const datum = dec * 1000
    const date = new Date(datum)
    return date
  },

  /**
   * Convierte Hexadecimal a Coordenadas
   *
   * @param {string} hex Parametro hexadecimal a convertir a Decimal
   *
   * @returns number
   */
  toCoordenada: function (hex) {
    let dec = this.toDecimal(hex)
    dec = Number(dec * (10 ** (-7)))
    return dec
  },

  toBin: function (hex) {
    hex = parseInt(hex, 16).toString(2)
    return hex
  },

  toSpeed: function (hex) {
    let x = Number(hex)
    if (isNaN(x)) {
      x = 0
    } else {
      x *= 0.036
    }
    return x
  },
  dec2hex: function (dec, bits) {
    let hex = Number(dec).toString(16)
    while (hex.length < (bits * 2)) {
      hex = '0' + hex
    }
    return hex
  }
}
