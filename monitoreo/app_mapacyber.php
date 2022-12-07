    <meta charset="UTF-8">
    <meta name="viewport" content="">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- jQuery plugin -->
    <script src="../librerias/jquery.min.js"></script>

    <!-- Bootstrap plugin -->
    <script src="../librerias/bootstrap/js/bootstrap.min.js"></script>
    <link href='../librerias/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">



    <script src="./librerias/openlayers/ol.js"></script>
    <link rel="stylesheet" href="./librerias/openlayers/ol.css">

    <style>
        .map-row {
            width: 100%;
            height: 500px;
        }

        .map {
            border: solid 1px gainsboro;
            position: static;
            width: 100%;
            height: 100% !important;
        }

        .tab-row {}

        .tab {
            border: solid 1px gainsboro;
            width: 100%;
        }
    </style>
    </head>

    <?php
    $economico = $_GET["economico"];


    $consulta = "SELECT * 
            FROM tb_remolques
            WHERE txt_economico_rem = ?";
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $economico);
    $query->execute();
    $registro = $query->fetch();
    $id = $registro["pk_clave_rem"];
    $latitud = $registro["num_latitud_rem"];
    $longitud = $registro["num_longitud_rem"];
    $query->closeCursor();
    ?>

    <body>

        <div class="container">

            <div class="col-sm-12">
                <div class="row map-row">
                    <div class="map" id="map"></div>
                </div>

                <div class="row tab-row">
                    <div class="tab" id="detalle">
                    </div>
                </div>
                <img src="http://maps.google.com/mapfiles/ms/icons/green.png" ;" alt="" srcset="" id="icon">

            </div>






            <script>
                $.ajax({
                        url: "./vehiculos/app_detallecyber.php?id=<?php echo $id; ?>",
                        cache: false
                    })
                    .done(function(html) {
                        document.getElementById("detalle").innerHTML = html;
                    });

                var map = new ol.Map({
                    target: 'map',
                    layers: [
                        new ol.layer.Tile({
                            source: new ol.source.OSM()
                        })
                    ],
                    view: new ol.View({
                        center: ol.proj.fromLonLat([<?php echo $longitud; ?>, <?php echo $latitud; ?>]),
                        zoom: 12
                    }),
                });

                var icon = new ol.Overlay({
                    element: document.getElementById('icon'),
                    position: ol.proj.fromLonLat([<?php echo $longitud; ?>, <?php echo $latitud; ?>])
                })

                map.addOverlay(icon);
            </script>




    </body>