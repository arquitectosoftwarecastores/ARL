'use strict'

const dbCredentials = require('../config/db')
const { Pool } = require('pg')

const poolARL = new Pool(dbCredentials.dbARL)
const poolAVL = new Pool(dbCredentials.dbAVL)

module.exports = {
  qryARL: async (text, params) => {
    try {
      const res = await poolARL.query(text, params)
      return res.rows
    } catch (error) {
      console.log('Error: ' + error)
      return []
    }
  },
  qryAVL: async (text, params) => {
    try {
      const res = await poolAVL.query(text, params)
      return res.rows
    } catch (error) {
      console.log('Error: ' + error)
      return []
    }
  }
}
