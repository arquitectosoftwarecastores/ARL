/**
 * Crea una alerta informativa en pantalla y desaparece en 20 segundos
 *
 * @param {String} message Mensaje a mostrar en la alerta
 * @param {String} alerttype Clase de estilo Bootstrap de la alerta
 */
function showAlert (message, alerttype) {
  // Crea alerta
  $('#alert_placeholder').append(
    '<div class="alert ' + alerttype + ' alert-dismissible fade show" role="alert" id="alertdiv">' +
    message +
    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
    '<span aria-hidden="true">&times;</span>' +
    '</button>' +
    '</div>'
  )

  // Elimina Alerta en 20 segundos
  setTimeout(function () {
    $('#alertdiv').remove()
  }, 20000)
}
