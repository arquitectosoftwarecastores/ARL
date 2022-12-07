/** Funciones para el Formulario */

/**
 * Añade una opcion al elemento Select
 *
 * @param {Element} select Elemento Select al que se desea añadir una opcion
 * @param {string} text Texto visual a mostrar para el Usuario
 * @param {String|Number} value Valor de la opción
 * @param {Boolean} disabled Habilita o deshabilita la opción por defecto esta habilitado
 */
function addOption (select, text, value, disabled) {
  const option = document.createElement('option')

  option.text = text
  option.value = value
  option.disabled = disabled

  select.add(option)
}

/**
 * Funcion con limpia los elementos del formulario
 *
 * @param {Element} select Elemento Select el cual será limpiado
 */
function clearOptions (select) {
  const len = select.length
  for (let j = 0; j < len; j++) {
    select.remove(0)
  }
}

/**
 * Limpia el formulario
 *
 * @param {Form} form Formulario que será limpiado
 */
function resetForm (form) {
  form.reset()
  $('.selectpicker').selectpicker('render')
  $('.selectpicker').selectpicker('refresh')
}
