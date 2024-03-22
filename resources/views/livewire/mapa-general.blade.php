<div>
    <h1>Mapa</h1>
    @if (auth()->user()->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL', 'C ENLACE DE MANZANA', 'MANZANAL']))
        <div class="relative">
            <label for="opcionesMapa" class="block text-sm font-medium text-gray-700">Mostrar en el mapa:</label>
            <select id="opcionesMapa" name="opcionesMapa" class=" text-gray-700 mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md mb-5">
                <option value="TODOS">TODO</option>
                @if (auth()->user()->hasRole(['MASTER', 'ADMIN']))
                    <option value="CASILLAS">Casillas</option>
                @endif
                @if (auth()->user()->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL']))
                    <option value="ZONAS">Zonas</option>
                    <option value="BARDAS">Bardas</option>
                @endif
                @if (auth()->user()->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL', 'C ENLACE DE MANZANA']))
                    <option value="SECCIONES">Secciones</option>
                    <option value="MANZANAS">Manzanas</option>
                @endif  
                @if (auth()->user()->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL', 'C ENLACE DE MANZANA', 'MANZANAL']))
                    <option value="MANZANAS">Manzanas</option>
                @endif  
                <option value="EJERCICIOS">Ejercicios</option>
            </select>
        </div>
    @endif
    <div wire:ignore id="mapaGeneral" style=" width:100%; height: 100vh;"></div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('mapaGeneral').setView([24.1353403, -110.2867958], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            }).addTo(map);

            // Función para limpiar el mapa
            function clearMap(map) {
                map.eachLayer(function (layer) {
                    if (!!layer.toGeoJSON) {
                        map.removeLayer(layer);
                    }
                });
            }

            // Función para cargar las capas en el mapa
            function loadMapLayers(selectedOption) {
                clearMap(map); // Limpia el mapa antes de cargar nuevas capas
                switch (selectedOption) {
                    case 'TODOS':
                        // Cargar todas las capas
                        loadZonas();
                        loadSecciones();
                        loadManzanas();
                        loadCasillas();
                        loadBardas();
                        loadEjercicios();
                        break;
                    case 'ZONAS':
                        loadZonas();
                        break;
                    case 'SECCIONES':
                        loadSecciones();
                        break;
                    case 'MANZANAS':
                        loadManzanas();
                        break;
                    case 'CASILLAS':
                        loadCasillas();
                        break;
                    case 'BARDAS':
                        loadBardas();
                        break;
                    case 'EJERCICIOS':
                        loadEjercicios();
                        break;
                }
            }
            
            function loadZonas() {
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
            }

            function loadSecciones() {
                var secciones = @json($secciones);

                // Define una paleta de colores para diferentes zonas.
                var paletaColores = ['#8C00AB', '#00AB8C', '#AB8C00', '#008CAB', '#AB008C'];

                function colorMasClaro(hex, luminosidad=0.1) {
                    // Valida que hex sea una cadena válida
                    if (typeof hex !== 'string' || hex[0] !== '#' || hex.length !== 7) {
                        console.error('colorMasClaro: valor hex inválido', hex);
                        return '#ffffff'; // Retorna blanco (o cualquier otro color por defecto) si hex no es válido
                    }

                    // Convertir hex a RGB
                    let rgb = parseInt(hex.slice(1), 16);
                    let r = (rgb >> 16) & 0xff;
                    let g = (rgb >> 8) & 0xff;
                    let b = rgb & 0xff;

                    // Aplicar la luminosidad
                    r = Math.round(r + (255 - r) * luminosidad);
                    g = Math.round(g + (255 - g) * luminosidad);
                    b = Math.round(b + (255 - b) * luminosidad);

                    // Convertir de nuevo a hex
                    rgb = (r << 16) | (g << 8) | b;
                    return '#' + (0x1000000 + rgb).toString(16).slice(1);
                }

                if (secciones != 0) {
                    secciones.forEach(function(seccion) {
                        // Asegura que seccion.zona sea definido y tenga un id numérico
                        if (seccion.asignacionesGeograficas.length > 0 && seccion.zona && typeof seccion.zona.id === 'number') {
                            var latlngs = seccion.asignacionesGeograficas.map(function(asignacion) {
                                return [asignacion.latitud, asignacion.longitud];
                            });

                            if (latlngs[0] !== latlngs[latlngs.length - 1]) {
                                latlngs.push(latlngs[0]);
                            }

                            // Usa seccion.zona.id en lugar de seccion.zonaId
                            var colorIndex = seccion.zona.id % paletaColores.length;
                            var color = paletaColores[colorIndex];
                            var colorRelleno = colorMasClaro(color, 0.7);
                            
                            var polygonOptions = {
                                color: color,
                                fillColor: colorRelleno,
                                fillOpacity: 0.5
                            };

                            var polygon = L.polygon(latlngs, polygonOptions).addTo(map);
                            map.fitBounds(polygon.getBounds());

                            var center = polygon.getBounds().getCenter();

                            var marker = L.marker(center, {opacity: 0}).addTo(map);
                            marker.bindTooltip(seccion.nombre, {
                                permanent: true,
                                className: 'custom-tooltip',
                                offset: [0, 0],
                                direction: 'center'
                            });
                        }
                    });
                }
            }

            function loadManzanas() {
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
            }

            function loadCasillas() {
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
            }

            function loadBardas() {
                var bardas = @json($bardas);
            
                if (bardas != 0){
                    // Definir un icono personalizado
                    var iconoBardaUrl = "{{ asset('images/icons/barda.png') }}";
                    var iconoBarda = L.icon({
                        iconUrl: iconoBardaUrl, // Usa la función helper asset() de Laravel
                        iconSize: [20, 20], // Tamaño del icono
                        iconAnchor: [20, 20], // Punto del icono que corresponderá a la ubicación del marcador
                        popupAnchor: [-3, -76] // Punto desde el que se abrirá el popup en relación al iconAnchor
                    });

                    bardas.forEach(function(barda) {
                        // Verifica que haya datos de asignación geográfica
                        if (barda.asignacionGeografica) {
                            var latitud = barda.asignacionGeografica.latitud;
                            var longitud = barda.asignacionGeografica.longitud;

                            // Crea un marcador en la posición de la asignación geográfica
                            var marker = L.marker([latitud, longitud], { icon: iconoBarda }).addTo(map);

                            // Adjunta un popup al marcador con información del barda
                            marker.bindPopup("<b>Identificador:</b> " + "<b>"+barda.identificador+"</b>" + "<br>" + "<b>Seccion:</b> " + "<b>"+barda.seccion.nombre+"</b>");
                        }
                    });
                }
            }
            
            function loadEjercicios() {
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
                            marker.bindPopup("<b>Folio:</b> " + "<b>"+ejercicio.folio+"</b>" + "<br>" + "<b>Lona:</b> " + "<b>"+ejercicio.lona+"</b>" + "<br>" + "<b>Por:</b> " + ejercicio.user.nombre + "<br>" + ejercicio.fecha);
                        }
                    });
                }
            }

            // Escuchar cambios en el select de opciones
            document.getElementById('opcionesMapa').addEventListener('change', function() {
                loadMapLayers(this.value);
            });

            // Carga inicial de todas las capas
            loadMapLayers('TODOS');
        });
    </script>
    @endpush
</div>
