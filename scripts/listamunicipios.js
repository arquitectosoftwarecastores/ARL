$(function(){
  $("select#estado").change(function(){
    $.getJSON("municipios/json_lista.php",{id: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#ciudad").html(options);
    })
  })
})