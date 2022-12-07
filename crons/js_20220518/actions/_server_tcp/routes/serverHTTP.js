'use strict'

module.exports = serverHTTP => {
  serverHTTP.post('/', async (req, res) => {
    const body = req
    console.log(body)
    res.send('0OK: ' + body)
  })
}
