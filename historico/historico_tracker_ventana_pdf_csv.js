
function crea_ventana_pdf()
{
    var ini   = document.getElementById('fecha_ini').value;
    var fin   = document.getElementById('fecha_fin').value;
    var id     = document.getElementById('id').value;
    var cia  = document.getElementById('cia').value;
    var ope  = document.getElementById('ope').value;
    var user  = document.getElementById('user').value;
    if (form_consulta.filtro[0].checked){
		vfiltro = form_consulta.filtro[0].value;
	}
	if (form_consulta.filtro[1].checked){
		vfiltro = form_consulta.filtro[1].value;
	}	
	if (form_consulta.filtro[2].checked){
		vfiltro = form_consulta.filtro[2].value;
	}
    
	nueva=window.open("historico_pdf.php?id="+id+"&ini="+ini+"&fin="+fin+"&filtro="+vfiltro+"&cia="+cia+"&ope="+ope+"&user="+user,""," location=0, toolbar=0,menubar=0,status=1,width=800, heigth=600");
}

function crea_ventana_csv()
{
    var ini   = document.getElementById('fecha_ini').value;
    var fin   = document.getElementById('fecha_fin').value;
    var id     = document.getElementById('id').value;
    var cia  = document.getElementById('cia').value;
    var ope  = document.getElementById('ope').value;
    var user  = document.getElementById('user').value;
    if (form_consulta.filtro[0].checked){
		vfiltro = form_consulta.filtro[0].value;
	}
	if (form_consulta.filtro[1].checked){
		vfiltro = form_consulta.filtro[1].value;
	}	
	if (form_consulta.filtro[2].checked){
		vfiltro = form_consulta.filtro[2].value;
	}
    
	nueva=window.open("historico_csv.php?id="+id+"&ini="+ini+"&fin="+fin+"&filtro="+vfiltro+"&cia="+cia+"&ope="+ope+"&user="+user,""," location=0, toolbar=0,menubar=0,status=1,width=800, heigth=600");
}