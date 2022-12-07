/**
 * Carga datos a una Tabla
 *
 * @param {Element} table Tabla a la que será añadida los datos
 * @param {JSON} data Datos de la tabla en formato JSON
 */
function setDataTable (table, data) {
  table.bootstrapTable('refresh')
  table.bootstrapTable({ data: data })
  table.bootstrapTable('load', data)
}