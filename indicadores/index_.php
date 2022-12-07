<iframe width="100%" height="80%" src="https://app.powerbi.com/reportEmbed?reportId=442c8490-3aab-4e4b-bb7a-68c8aa426c85&autoAuth=true&ctid=5b13a8bb-3215-43bd-9061-299770002d07&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly93YWJpLXBhYXMtMS1zY3VzLXJlZGlyZWN0LmFuYWx5c2lzLndpbmRvd3MubmV0LyJ9" frameborder="0" allowFullScreen="true" id="iframeBI"></iframe>


<script src="funciones/cookies.js"></script>

<script>
  function refreshBI () {
    const reportName = 'iframeBI'
    const report = document.getElementById(reportName);

    // Obitiene Cookie
    let cookieBI = getCookie(reportName)

    // Valida Existencia de la Cookie
    if (cookieBI != '') {
      // Valida Fecha
      const now = new Date()
      cookieBI = new Date(cookieBI)

      if (cookieBI < now) {
        // Actualiza Informacion
        setCookieBi(reportName)
        refreshBiData(report)
      }
    } else {
      // Genera Cookie
      setCookieBi(reportName)
      // Actualiza Data y Frame
      refreshBiData(report)
    }
  }

  function setCookieBi (reportName) {
    let now10 = new Date()
    now10.setMinutes(now10.getMinutes() + 10)
    setCookie(reportName, now10)
  }

  function refreshBiData (report) {
    const urlBi = 'https://api.powerbi.com/v1.0/myorg/datasets/7ce5d571-b790-4c29-810c-89368cfce985/refreshes'
    const authorization = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6Im5PbzNaRHJPRFhFSzFqS1doWHNsSFJfS1hFZyIsImtpZCI6Im5PbzNaRHJPRFhFSzFqS1doWHNsSFJfS1hFZyJ9.eyJhdWQiOiJodHRwczovL2FuYWx5c2lzLndpbmRvd3MubmV0L3Bvd2VyYmkvYXBpIiwiaXNzIjoiaHR0cHM6Ly9zdHMud2luZG93cy5uZXQvNWIxM2E4YmItMzIxNS00M2JkLTkwNjEtMjk5NzcwMDAyZDA3LyIsImlhdCI6MTYxNTQ4NDYzOCwibmJmIjoxNjE1NDg0NjM4LCJleHAiOjE2MTU0ODg1MzgsImFjY3QiOjAsImFjciI6IjEiLCJhaW8iOiJFMlpnWU9CenZUTlgzTEc2K2wzTko0VlhhMFJVZnM1UFBLei85cFc0SG1lTTh2V1NtNTBBIiwiYW1yIjpbInB3ZCJdLCJhcHBpZCI6IjdmNTlhNzczLTJlYWYtNDI5Yy1hMDU5LTUwZmM1YmIyOGI0NCIsImFwcGlkYWNyIjoiMiIsImZhbWlseV9uYW1lIjoiQ29ycG9yYXRpdm8iLCJnaXZlbl9uYW1lIjoiQW5hbGlzdGEgZGUgQmFzZXMgZGUgRGF0b3MiLCJpcGFkZHIiOiIyMDEuMTQ0LjQ5LjIyNiIsIm5hbWUiOiJBbmFsaXN0YSBkZSBCYXNlcyBkZSBEYXRvcyBDb3Jwb3JhdGl2byIsIm9pZCI6ImU1ZjgyMTE5LTRhMmYtNDFjYS05OTVhLTI5NDZhYTQxZjkyYSIsInB1aWQiOiIxMDAzMjAwMDY3RjBGRjNBIiwicmgiOiIwLkFTd0F1NmdUV3hVeXZVT1FZU21YY0FBdEIzT25XWC12THB4Q29GbFFfRnV5aTBRc0FGSS4iLCJzY3AiOiJ1c2VyX2ltcGVyc29uYXRpb24iLCJzdWIiOiJCQjViWC13cFQ2ekxpSlZjcGVHZTMxZGpvSFZKdDM3OTVHMkhRdHphc1pZIiwidGlkIjoiNWIxM2E4YmItMzIxNS00M2JkLTkwNjEtMjk5NzcwMDAyZDA3IiwidW5pcXVlX25hbWUiOiJhbmFsaXN0YWJkMUBjYXN0b3JlczEub25taWNyb3NvZnQuY29tIiwidXBuIjoiYW5hbGlzdGFiZDFAY2FzdG9yZXMxLm9ubWljcm9zb2Z0LmNvbSIsInV0aSI6IlVTRjRzb3BzWDBHTWRURFF0QU5XQUEiLCJ2ZXIiOiIxLjAiLCJ3aWRzIjpbImI3OWZiZjRkLTNlZjktNDY4OS04MTQzLTc2YjE5NGU4NTUwOSJdfQ.USdm_o6zecmAsVdkoFyO60fSahECRSspbdmtY5m4fm7nvjJ4hIUn5xjAKcYkMmKXeG5YfJ7IfRgT2umajsnqJ1_6YzRnJC2BekCLjnJikKEqX3KXmtIpymOH82WV9cNX0h-dW4hoz2rYeQ2Ga0ZHsi-X09fd_2-R4Pt0qENefn2ev_QwB5X4M8K-o0nkwapP_LLqyzJoNUtCn0v93DBroNFNgF1d4riaNFeqD1pJWP2tLdzqLhdr2-4To_mbo8pCspaHFSHzB3jBegLZtAgGUYi3FuWTWHUW1JHm0bm2_zOm9uDQ6RZK2cTeJl5zqrnn1sdo8cqIQVM0zkY8mkspnA'

    $.ajax({
      url: urlBi,
      type: 'POST',
      cache: false,
      headers: {
        'Authorization': authorization,
        'content-type': 'application/json',
        'requestid': '91617d1e-2679-47d6-9122-65e3a9b9ee54'
      },
      data: {
        notifyOption: ""
      },
      success: (res) => {},
      complete: function () {
        report.src = report.src;
      }
    })

  }
</script>