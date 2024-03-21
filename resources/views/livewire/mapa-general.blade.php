<div>
    <h1>Mapa</h1>
    <div wire:ignore id="mapaGeneral" style=" width:100%; height: 100vh;"></div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('mapaGeneral').setView([24.1353403, -110.2867958], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            }).addTo(map);

            //ZONAS
            
            var zonas = @json($zonas);
            
            if (zonas != 0){
                zonas.forEach(function(zona) {
                    if (zona.asignacionesGeograficas.length > 0) {
                        var latlngs = zona.asignacionesGeograficas.map(function(asignacion) {
                            return [asignacion.latitud, asignacion.longitud];
                        });

                        // Cierra el polígono si el primer y último punto no son iguales
                        if (latlngs[0] !== latlngs[latlngs.length - 1]) {
                            latlngs.push(latlngs[0]);
                        }
                        
                        var polygon = L.polygon(latlngs, { color: '#009EF3', fillColor: '#06DBF9', fillOpacity: 0.1 }).addTo(map);
                        map.fitBounds(polygon.getBounds());

                        // Calcula el centro del polígono para colocar la etiqueta
                        var center = polygon.getBounds().getCenter();

                        // Crea un marcador invisible en el centro del polígono
                        var marker = L.marker(center, {opacity: 0}).addTo(map);

                        // Adjunta un tooltip al marcador
                        marker.bindTooltip(zona.nombre, {
                            permanent: true, // Hace que el tooltip sea siempre visible
                            className: 'custom-tooltip', // Clase para estilos personalizados
                            offset: [0, 0], // Centra el tooltip en el marcador
                            direction: 'center' // Asegura que el tooltip se muestre en el centro
                        });
                    }
                });
            }

            //
  
            //SECCIONES
            
            var secciones = @json($secciones);
            
            if (secciones != 0){
                secciones.forEach(function(seccion) {
                    if (seccion.asignacionesGeograficas.length > 0) {
                        var latlngs = seccion.asignacionesGeograficas.map(function(asignacion) {
                            return [asignacion.latitud, asignacion.longitud];
                        });

                        // Cierra el polígono si el primer y último punto no son iguales
                        if (latlngs[0] !== latlngs[latlngs.length - 1]) {
                            latlngs.push(latlngs[0]);
                        }
                        
                        var polygon = L.polygon(latlngs, { color: '#8C00AB', fillColor: '#C981D9', fillOpacity: 0.1 }).addTo(map);
                        map.fitBounds(polygon.getBounds());

                        // Calcula el centro del polígono para colocar la etiqueta
                        var center = polygon.getBounds().getCenter();

                        // Crea un marcador invisible en el centro del polígono
                        var marker = L.marker(center, {opacity: 0}).addTo(map);

                        // Adjunta un tooltip al marcador
                        marker.bindTooltip(seccion.nombre, {
                            permanent: true, // Hace que el tooltip sea siempre visible
                            className: 'custom-tooltip', // Clase para estilos personalizados
                            offset: [0, 0], // Centra el tooltip en el marcador
                            direction: 'center' // Asegura que el tooltip se muestre en el centro
                        });
                    }
                });
            }

            //

            //MANZANAS
            
            var manzanas = @json($manzanas);
            
            if (manzanas != 0){
                manzanas.forEach(function(manzana) {
                    if (manzana.asignacionesGeograficas.length > 0) {
                        var latlngs = manzana.asignacionesGeograficas.map(function(asignacion) {
                            return [asignacion.latitud, asignacion.longitud];
                        });

                        // Cierra el polígono si el primer y último punto no son iguales
                        if (latlngs[0] !== latlngs[latlngs.length - 1]) {
                            latlngs.push(latlngs[0]);
                        }
                        
                        var polygon = L.polygon(latlngs, { color: '#DE8000', fillColor: '#F3B663', fillOpacity: 0.1 }).addTo(map);
                        map.fitBounds(polygon.getBounds());

                        // Calcula el centro del polígono para colocar la etiqueta
                        var center = polygon.getBounds().getCenter();

                        // Crea un marcador invisible en el centro del polígono
                        var marker = L.marker(center, {opacity: 0}).addTo(map);

                        // Adjunta un tooltip al marcador
                        marker.bindTooltip(manzana.nombre, {
                            permanent: true, // Hace que el tooltip sea siempre visible
                            className: 'custom-tooltip', // Clase para estilos personalizados
                            offset: [0, 0], // Centra el tooltip en el marcador
                            direction: 'center' // Asegura que el tooltip se muestre en el centro
                        });
                    }
                });
            }

            //

            //EJERCICIOS
            
            var ejercicios = @json($ejercicios);
            
            if (ejercicios != 0){
                var greenIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                    iconSize: [18, 22],
                    iconAnchor: [5, 5],
                    popupAnchor: [1, -34],
                    shadowSize: [2, 2]
                });

                var blueIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                    iconSize: [18, 22],
                    iconAnchor: [5, 5],
                    popupAnchor: [1, -34],
                    shadowSize: [2, 2]
                });

                var redIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    iconSize: [18, 22],
                    iconAnchor: [5, 5],
                    popupAnchor: [1, -34],
                    shadowSize: [2, 2]
                });

                var yellowIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-yellow.png',
                    iconSize: [18, 22],
                    iconAnchor: [5, 5],
                    popupAnchor: [1, -34],
                    shadowSize: [2, 2]
                });

                var chooseIcon;

                ejercicios.forEach(function(ejercicio) {
                    // Verifica que haya datos de asignación geográfica
                    if (ejercicio.asignacionGeografica) {
                        var latitud = ejercicio.asignacionGeografica.latitud;
                        var longitud = ejercicio.asignacionGeografica.longitud;

                        if (ejercicio.respuesta == 'SI'){
                            chooseIcon = greenIcon;
                        } else if (ejercicio.respuesta == 'NO'){
                            chooseIcon = redIcon;
                        } else if (ejercicio.respuesta == 'INDECISO') {
                            chooseIcon = yellowIcon;
                        } else {
                            chooseIcon = blueIcon;
                        }

                        // Crea un marcador en la posición de la asignación geográfica
                        var marker = L.marker([latitud, longitud], { icon: chooseIcon }).addTo(map);

                        // Adjunta un popup al marcador con información del ejercicio
                        marker.bindPopup("<b>Folio:</b> " + "<b>"+ejercicio.folio+"</b>" + "<br>" + "<b>Por:</b> " + ejercicio.user.nombre + "<br>" + ejercicio.fecha);
                    }
                });
            }

            //
            
            //CASILLAS
            
            var casillas = @json($casillas);
            
            if (casillas != 0){
                // Definir un icono personalizado
                var iconoCasillaUrl = "{{ asset('images/icons/casilla-de-votacion.png') }}";
                var iconoCasilla = L.icon({
                    iconUrl: iconoCasillaUrl, // Usa la función helper asset() de Laravel
                    iconSize: [20, 20], // Tamaño del icono
                    iconAnchor: [20, 20], // Punto del icono que corresponderá a la ubicación del marcador
                    popupAnchor: [-3, -76] // Punto desde el que se abrirá el popup en relación al iconAnchor
                });

                casillas.forEach(function(casilla) {
                    // Verifica que haya datos de asignación geográfica
                    if (casilla.asignacionGeografica) {
                        var latitud = casilla.asignacionGeografica.latitud;
                        var longitud = casilla.asignacionGeografica.longitud;

                        // Crea un marcador en la posición de la asignación geográfica
                        var marker = L.marker([latitud, longitud], { icon: iconoCasilla }).addTo(map);

                        // Adjunta un popup al marcador con información del casilla
                        marker.bindPopup("<b>Número de Casilla:</b> " + "<b>"+casilla.numero+"</b>");
                    }
                });
            }

            //
        });
    </script>
    @endpush
</div>
