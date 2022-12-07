'use strict'

const hex = require('./encodeString')
const db = require('../db')
const { toBin } = require('./encodeString')

/**
 * Clase de para las Cadenas Hexadecimales
 */
module.exports = class HexString {
  /** Constructor */

  /**
   * Inicializa el objeto con la Cadena del GPS
   * Automaticamente Obtiene Encabezados
   *
   * @param {string} rawString Cadena obtenida del GPS
   */
  constructor (rawString) {
    if (rawString !== undefined) {
      // Valida si se mando la cadena
      this.rawString = rawString
      this.string = this.rawString
    }
  }

  /** Metodos */

  /**
   * Almacena cadena sin procesar en Objeto
   *
   * @param {string} rawString Cadena sin procesar
   */
  setRawString (rawString) {
    this.rawString = rawString
    this.string = this.rawString
  }

  /** Headers */

  /**
   * Obtiene Headers de la cadena
   */
  processHeaders () {
    this.processOptionHeader()
    this.processMessageHeader()
  }

  /**
   *
   */
  processOptionHeader () {
    let mobileID, mobileType
    let optionBytes = this.getHexStr(1)
    let raw = optionBytes
    optionBytes = hex.toBin(optionBytes)

    for (let i = optionBytes.length; i >= 0; i--) {
      const option = optionBytes[i]
      if (option === '1') {
        const opt = 7 - i
        switch (opt) {
          case 0:
            /** Mobile ID */
            mobileID = this.getHexStr(1)
            raw += mobileID
            mobileID = hex.toDecimal(mobileID)
            mobileID = this.getHexStr(mobileID)
            raw += mobileID
            break

          case 1:
            /** Mobile ID Type */
            mobileType = this.getHexStr(1)
            raw += mobileType
            mobileType = hex.toDecimal(mobileType)
            mobileType = this.getHexStr(mobileType)
            raw += mobileType
            break

          case 2:
            /** Authentication Word */
            break

          case 3:
            /** Routing */
            break

          case 4:
            /** Forwarding */
            break

          case 5:
            /** Response Redirection */
            break

          case 6:
            /** Not Used – Reserved for future use */
            break

          case 7:
            /** Always set */
            break

          default:
            break
        }
      }
    }

    this.optionHeader = {
      raw: raw,
      optionByte: optionBytes,
      mobileID: mobileID,
      mobileType: mobileType
    }
  }

  /**
   *
   */
  processMessageHeader () {
    const serviceType = this.getHexStr(1)
    const messageType = this.getHexStr(1)
    const messageSeq = this.getHexStr(2)

    this.messageHeader = {
      serviceType: serviceType,
      messageType: messageType,
      messageSeq: messageSeq
    }
  }

  /** Body */

  processMessageBody () {
    switch (this.messageHeader.messageType) {
      case '00':
        console.log('** NULL Message **')
        break

      case '01':
        console.log('** ACK/NAK Message **')
        this.processMessageAcknowledge()
        break

      case '02':
        console.log('** Event Report Message **')
        this.processMessageReport()
        break

      case '03':
        console.log('** ID Report Message **')
        break

      case '04':
        console.log('** User Data Message **')
        break

      case '05':
        console.log('** ID Report Message **')
        break

      default:
        console.log('** Unknown Message **')
        break
    }
  }

  /**
   *
   */
  processMessageAcknowledge () {
    const type = this.getHexStr(1)
    const ack = this.getHexStr(1)
    const spare = this.getHexStr(1)
    const appVer = this.getHexStr(3)

    this.message = {
      type: type,
      ack: ack,
      spare: spare,
      appVersion: appVer
    }
  }

  /**
   *
   * @param {string} rawString Cadena codificada en Hexadecimal
   */
  async processMessageReport (rawString) {
    const updateTime = this.getHexStr(4)
    const fixTime = this.getHexStr(4)
    const latitude = this.getHexStr(4)
    const longitude = this.getHexStr(4)
    const altitude = this.getHexStr(4)
    const speed = this.getHexStr(4)
    const heading = this.getHexStr(2)
    const satellites = parseInt(this.getHexStr(1), 16)
    const fixStatus = hex.toBin(this.getHexStr(1))
    const carrier = parseInt(this.getHexStr(2), 16)
    const rssi = hex.toDecimal(this.getHexStr(2))
    const commState = hex.toBin(this.getHexStr(1))
    const hdop = this.getHexStr(1)
    const inputs = hex.toBin(this.getHexStr(1))
    const unitStatus = hex.toBin(this.getHexStr(1))
    const eventIndex = this.getHexStr(1)
    const eventCode = parseInt(this.getHexStr(1), 16)
    const accums = hex.toDecimal(this.getHexStr(1))
    const spare = this.getHexStr(1)

    // CommState

    let ignition = 0
    let motion = 0
    let powerState = 0
    let bateraInterna = 0
    let highTemp = 0

    let countInputs = 0

    // Inputs
    for (let i = (inputs.length - 1); i >= 0; i--) {
      const input = inputs[i]

      if (input === '1') {
        const posin = 7 - countInputs
        switch (posin) {
          case 7:
            // Ignicion - bit 0
            ignition = 1
            break

          case 3:
            // Motion - bit 4
            motion = 1
            break

          case 2:
            // Power State - bit 5
            powerState = 1
            break

          case 1:
            // Bateria Interna - bit 6
            bateraInterna = 1
            break

          case 0:
            // High Temp - bit 7
            highTemp = 1
            break

          default:
            break
        }
      }
      countInputs++
    }

    // Accums
    let charge = null
    let voltage = null
    let accum3 = null
    let idButton = null

    for (let i = 0; i < accums; i++) {
      switch (i) {
        case 0:
          // Nivel de Carga
          charge = hex.toDecimal(this.getHexStr(4))
          break

        case 1:
          // Voltaje
          voltage = hex.toDecimal(this.getHexStr(4))
          break

        case 3:
          // Accum3
          accum3 = hex.toDecimal(this.getHexStr(4))
          break

        case 7:
          // ID Button
          idButton = this.getHexStr(4)

          if (idButton === '00000000') {
            idButton = null
          }
          break

        default:
          this.getHexStr(4)
          break
      }
    }

    this.message = {
      updateTime: hex.toDate(updateTime),
      fixTime: hex.toDate(fixTime),
      latitude: hex.toCoordenada(latitude),
      longitude: hex.toCoordenada(longitude),
      altitude: hex.toDecimal(altitude),
      speed: hex.toSpeed(speed),
      heading: hex.toDecimal(heading),
      satellites: satellites,
      fixStatus: fixStatus,
      carrier: carrier,
      rssi: rssi,
      commState: commState,
      inputs: inputs,
      ignition: ignition,
      motion: motion,
      powerState: powerState,
      unitStatus: unitStatus,
      eventCode: eventCode,
      charge: charge,
      voltage: voltage,
      accum3: accum3,
      idButton: idButton
    }
  }

  /**
   * Valida Generacion de alerta con la informacion procesada previamente
   *
   * @param {Array} arr Array que contiene ID del tipo de Evento y Alerta
   */
  validaAlertas (arr) {
    console.log(`Event Code: ${this.message.eventCode}`)
    // Valida Existencia de Evento
    for (let i = 0; i < arr.length; i++) {
      const ale = arr[i]

      if (ale.event == this.message.eventCode & ale.alert !== null) {
        // Genera Alerta del Evento
        console.log(' - Genera Alerta de Evento ' + ale.alert + ' - NSerie: ' + this.optionHeader.mobileID)
        this.insertAlerta(ale.alert)
        break
      }
    }

    // Valida Voltaje
    if (this.message.voltage !== null & this.message.voltage <= 3700) {
      // Genera Alerta de Bateria Baja
      console.log(' - Genera Alerta de Bateria Baja ' + this.message.voltage + ' - NSerie: ' + this.optionHeader.mobileID)
      this.insertAlerta(204)
    }

    //
    if (this.message.motion === 1 & this.message.ignition === 0) {
      console.log(' - Genera Alerta de Movimiento Sin Conexión - NSerie: ' + this.optionHeader.mobileID)
      this.insertAlerta(209)
    }
  }

  /* Base de Datos */

  /**
   * Inserta cadena en crudo en avl_cadenas_g
   *
   * @param {string} ip IP del GPS que envió la cadena
   */
  insertCadenasg (ip) {
    const inCad = `INSERT INTO avl_cadenas_g 
                    (cad_tipo, cad_nserie, cad_string, cad_ip, cad_numero) 
                  VALUES ($1, $2, $3, $4, $5)`
    const valCad = [
      this.messageHeader.messageType,
      this.optionHeader.mobileID,
      this.rawString,
      ip,
      1
    ]
    db.qryARL(inCad, valCad)
  }

  /**
   * Inserta Posicion del Remolque en tb_posiciones
   *
   * @param {Number} idcad Id de la cadena en avl_cadenas_g
   */
  insertPosicion (idcad) {
    const inCad = `INSERT INTO tb_posiciones 
                    (txt_tipo_pos, txt_nserie_pos, fec_ultimaposicion_pos, 
                      num_latitud_pos, num_longitud_pos, num_altitud_pos, 
                      num_velocidad_pos, num_orientacion_pos, num_ignicion_pos, 
                      txt_inputs_pos, txt_coderr_pos, num_idmsg_pos, txt_cadena_pos,
                      num_event_pos, num_voltage_pos, num_charge_pos, txt_idbutton_pos,
                      num_satellites_pos, txt_fixstatus_pos, num_carrier_pos,
                      num_rssi_pos, txt_commstate_pos, txt_unitstatus_pos,
                      num_motion_pos, num_powerstate_pos, txt_accum3_pos) 
                  VALUES 
                    ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, 
                      $11, $12, $13, $14, $15, $16, $17, $18, $19, 
                      $20, $21, $22, $23, $24, $25, $26)`
    const valCad = [
      this.messageHeader.messageType,
      this.optionHeader.mobileID,
      this.message.updateTime,
      this.message.latitude,
      this.message.longitude,
      this.message.altitude,
      this.message.speed,
      this.message.heading,
      this.message.ignition,
      this.message.inputs,
      this.message.fixStatus,
      idcad,
      this.rawString,
      this.message.eventCode,
      this.message.voltage,
      this.message.charge,
      this.message.idButton,
      this.message.satellites,
      this.message.fixStatus,
      this.message.carrier,
      this.message.rssi,
      this.message.commState,
      this.message.unitStatus,
      this.message.motion,
      this.message.powerState,
      this.message.accum3
    ]
    db.qryARL(inCad, valCad)
  }

  /**
   * Inserta alerta llamando a la funcion de BD -insertarAlerta-
   *
   * @param {Number} idTipAle Identificador de tipo de alerta
   */
  insertAlerta (idTipAle) {
    const inAlt = 'SELECT monitoreo.genera_alerta ($1, $2, $3, $4, $5, $6, $7);'
    const valAlt = [
      this.optionHeader.mobileID,
      idTipAle,
      this.message.updateTime,
      this.message.ignition,
      this.message.latitude,
      this.message.longitude,
      this.rawString
    ]
    db.qryARL(inAlt, valAlt)
  }

  /**
   * Actualiza el estatus a la cadena ya procesada
   *
   * @param {Number} idCad ID de la cadena a actualizar en avl_cadenas_g
   */
  async updateCadenasg (idCad) {
    const inCad = 'UPDATE avl_cadenas_g SET cad_estatus = 0, cad_fechahora = $1 WHERE cad_id = $2'
    const valCad = [this.message.updateTime, idCad]
    await db.qryARL(inCad, valCad)
  }

  /**
   * Metodo para obtener campos
   *
   * @param {Number} bytes Longitud de Bytes que ocupa el campo
   *
   * @returns {String}
   */
  getHexStr (bytes) {
    bytes *= 2
    const subHex = this.string.substr(0, bytes)
    this.string = this.string.substring(bytes)
    return subHex
  }

  /**
   * Obtiene cadena de respuesta a GPS predeterminada
   */
  getDefaultRes () {
    let cmd = this.optionHeader.raw + '0201' + this.messageHeader.messageSeq + '020000000000'
    const cmdLen = this.getStrLen(cmd)
    cmd = 'aa55' + cmdLen + cmd

    return cmd
  }

  getCommand (serie, action, data8, data16, data32) {
    this.cmd = {
      optionByte: '83',
      mobileIDLength: '05',
      mobileID: serie,
      mobileIDTypeLength: '01',
      mobileIDType: '01',
      serviceType: '01',
      messageType: '07',
      sequenceNumber: '0000',
      action: hex.dec2hex(action, 1),
      data8: hex.dec2hex(data8, 1),
      data16: hex.dec2hex(data16, 2),
      data32: hex.dec2hex(data32, 4),
      raw: ''
    }

    const bodyCmd = this.cmd.optionByte + this.cmd.mobileIDLength + this.cmd.mobileID +
                    this.cmd.mobileIDTypeLength + this.cmd.mobileIDTypeLength +
                    this.cmd.serviceType + this.cmd.messageType +
                    this.cmd.sequenceNumber + this.cmd.action +
                    this.cmd.data8 + this.cmd.data16 + this.cmd.data32
    const lenCmd = this.getStrLen(bodyCmd)

    this.cmd.raw = 'aa55' + lenCmd + bodyCmd
  }

  /**
   *
   * @param {String} str
   */
  getStrLen (str) {
    let cmdLen = str.length / 2
    cmdLen = cmdLen.toString(16)
    while (cmdLen.length < 4) {
      cmdLen = '0' + cmdLen
    }
    return cmdLen
  }
}
