let listSer = []
let listErr = []
let formCmd = document.getElementById('formComandos')
let formCmdElem = formCmd.elements

document.getElementById('progress').style.width = 0 + '%'

generaComando()

function addEconomico () {
  const e = document.getElementById('selEconomico')
  const opt = e.options[e.selectedIndex]

  const eco = opt.text
  const ser = opt.value

  // Valida si el remolque ya fue añadido a la ista
  let exist = false
  for (let i = 0; i < listSer.length; i++) {
    const rem = listSer[i].serie

    if (rem == ser) {
      exist = true
    }
  }

  // Realiza validacion
  if (ser !== '' & exist === false) {
    // Añade el remolque a la lista
    document.getElementById('listEconomico').innerHTML += '<button class="btn btn-sm btn-primary mr-2 mb-2" id="' + ser + '" onclick="deleteEconomico(' + ser + ')">' + eco +
            '&nbsp;&nbsp;<span aria-hidden="true" style="font-weight: bold; font-size: 14px">&times;</span>' +
          '</button>'

    const remolque = {
      serie: ser,
      economico: eco
    }
    listSer.push(remolque)
  }
}

function deleteEconomico (serie) {
  // Eliminar remolque de la lista
  for (let i = 0; i < listSer.length; i++) {
    const rem = listSer[i].serie
    if (rem == serie) {
      const elem = document.getElementById(serie)
      elem.remove()
      listSer.splice(i, 1)
      break
    }
  }
}

function generaComando () {
  let action = Number(document.getElementById('txtAction').value)
  let data8 = Number(document.getElementById('txtData8').value)
  let data16 = Number(document.getElementById('txtData16').value)
  let data32 = Number(document.getElementById('txtData32').value)

  action = dec2hex(action, 1)
  data8 = dec2hex(data8, 1)
  data16 = dec2hex(data16, 2)
  data32 = dec2hex(data32, 4)

  let cmd = '8001070000' + action + data8 + data16 + data32

  for (let i = 0; i < cmd.length; i++) {
    if ((i % 3) === 0) {
      cmd = cmd.substring(0, i) + ' ' + cmd.substring(i)
    }
  }

  document.getElementById('txtCMD').value = cmd
}

function disabledButtons (val) {
  document.getElementById('btnLimpiarForm').disabled = val
  document.getElementById('btnSubmitCmd').disabled = val

  for (let i = 0; i < listSer.length; i++) {
    const serie = listSer[i].serie
    document.getElementById(serie).disabled = val
    if (val) {
      document.getElementById(serie).className = 'btn btn-sm btn-primary mr-2 mb-2'
    }
  }
}

/**
 *
 * @param {Number} dec Valor decimal
 * @param {Number} bits Numero de bits
 */
function dec2hex (dec, bits) {
  let hex = dec.toString(16)

  while (hex.length < (bits * 2)) {
    hex = '0' + hex
  }

  return hex
}

/** Funciones de Submit */

async function actionSubmitCMD () {
  document.getElementById('progress').style.width = '0%'
  disabledButtons(true)
  // Envia Comandos
  try {
    for (let i = 0; i < listSer.length; i++) {
      const rem = listSer[i]
      setTimeout(async () => {
        // Envia Comando
        await enviaComando(rem)
        // Barra de Progreso
        const perc = ((i + 1) * 100) / listSer.length
        document.getElementById('progress').style.width = perc + '%'

        if (perc === 100) {
          disabledButtons(false)
        }
      }, (240 * (i + Math.random())))
    }
    formCmdElem.selEconomico.value = ''
  } catch (error) {
  }
  return false
}

async function enviaComando (remolque) {
  const serie = remolque.serie
  const economico = remolque.economico

  const con = document.getElementById('txtConsole').value +
  '\n---- Enviando Comando ----\n- No Eco: ' + economico + '\n- ESN: ' + serie + '\n'
  formCmdElem.txtConsole.value = con
  $.ajax({
    url: 'http://arl.castores.com.mx:3330/command',
    data: {
      serie: serie,
      action: formCmdElem.txtAction.value,
      data8: formCmdElem.txtData8.value,
      data16: formCmdElem.txtData16.value,
      data32: formCmdElem.txtData32.value
    },
    method: 'post',
    cache: false,
    async: false
  }).done((res) => {
    if (res.res === '0OK') {
      document.getElementById(serie).className = 'btn btn-sm btn-success mr-2 mb-2'
      const con = document.getElementById('txtConsole').value +
                  'Envio Exitoso ✔️\n'
      formCmdElem.txtConsole.value = con
    } else {
      listErr.push(serie)

      document.getElementById(serie).className = 'btn btn-sm btn-danger mr-2 mb-2'
      const con = formCmdElem.txtConsole.value +
                  'Problemas al enviar comando ❌\n'
      formCmdElem.txtConsole.value = con
    }
    formCmdElem.txtConsole.scrollTop = formCmdElem.txtConsole.scrollHeight
  })
}

function cleanFormCMD() {
  document.getElementById('progress').style.width = '0%'
  const lenSeries = listSer.length
  for (let i = 0; i < lenSeries; i++) {
    const serie = listSer[0].serie
    deleteEconomico(serie)
  }
}