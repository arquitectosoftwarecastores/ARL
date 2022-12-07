<?php
	$strSQL  = "SELECT uu.noeconomico, uu.fecha_ingreso::timestamp - '5 hr'::interval as fecha_ingreso, now()::timestamp - '5 hr'::interval as fechaactualb,
	r.num_latitud_rem as latitud, r.num_longitud_rem as longitud, uu.km as km,
	EXTRACT(DAY FROM now() - uu.fecha_ingreso) as dias
	FROM monitoreo.unidades_usa uu
	join tb_remolques r on r.txt_economico_rem = uu.noeconomico order by 2 asc";
?>