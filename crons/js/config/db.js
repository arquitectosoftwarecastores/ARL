'use strict';

require('dotenv').config();

module.exports = {
  db13: {
    host: process.env.DB13_HOST,
    port: process.env.DB13_PORT,
    user: process.env.DB13_USER,
    password: process.env.DB13_PASSWORD
  },
  db23: {
    host: process.env.DB23_HOST,
    port: process.env.DB23_PORT,
    user: process.env.DB23_USER,
    password: process.env.DB23_PASSWORD
  },
  dbAVL: {
    host: process.env.DBAVL_HOST,
    port: process.env.DBAVL_PORT,
    database: process.env.DBAVL_DB,
    user: process.env.DBAVL_USER,
    password: process.env.DBAVL_PASSWORD
  },
  dbARL: {
    host: process.env.DBARL_HOST,
    port: process.env.DBARL_PORT,
    database: process.env.DBARL_DB,
    user: process.env.DBARL_USER,
    password: process.env.DBARL_PASSWORD
  },
  dbMON: {
    host: process.env.DBMONGO_HOST,
    port: process.env.DBMONGO_PORT
  }
};
