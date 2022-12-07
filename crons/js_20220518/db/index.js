'use strict';

const dbCredentials = require('../config/db');
const mysql = require('mysql2');

const { Pool } = require('pg');

const poolARL = new Pool(dbCredentials.dbARL);
const poolAVL = new Pool(dbCredentials.dbAVL);
const pool13 = mysql.createPool(dbCredentials.db13).promise();
const pool23 = mysql.createPool(dbCredentials.db23).promise();

module.exports = {
  qryARL: async (text, params) => {
    try {
      const res = await poolARL.query(text, params);
      return res.rows;
    } catch (error) {
      console.log('Error: ' + error);
      return [];
    }
  },
  qryAVL: async (text, params) => {
    try {
      const res = await poolAVL.query(text, params);
      return res.rows;
    } catch (error) {
      console.log('Error: ' + error);
      return [];
    }
  },
  qry13: (text, params) => pool13.query(text, params),
  /**
   * Funcion para realizar Querys en Base de Datos del Servidor 23
   * @param {String} text Query a realizar
   * @param {Array} params Array con valores a insertar
   * @returns Objeto con respuesta de Base de datos
   */
  qry23: (text, params) => pool23.query(text, params)
};
