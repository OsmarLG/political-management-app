<h1>Mapa {{ $getRecord()->folio ?? '' }}</h1>
<div wire:ignore id="mapaEjercicio" style=" width:100%; height: 400px;"></div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

        var folio = @json($getRecord()->folio);
        var user = @json($getRecord()->user);
        var latitud = @json($getRecord()->asignacionGeografica->latitud);
        var longitud = @json($getRecord()->asignacionGeografica->longitud);
        var fecha = @json($getRecord()->created_at->diffForHumans());
        
        var map = L.map('mapaEjercicio').setView([latitud, longitud], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        }).addTo(map);

        var lona = @json($getRecord()->respuestas[2]['respuesta']);

        var name = @json($getRecord()->user->name . '' . $getRecord()->user->apellido_paterno . ' ' . $getRecord()->user->apellido_materno)

        if (@json($getRecord()->a_favor == 'A FAVOR')){
            chooseIcon = greenIcon;
        } else if (@json($getRecord()->a_favor == 'EN DESACUERDO')){
            chooseIcon = redIcon;
        } else if (@json($getRecord()->a_favor == 'INDECISO')){
            chooseIcon = yellowIcon;
        } else {
            chooseIcon = blueIcon;
        }
        // Crea un marcador en la posición de la asignación geográfica
        var marker = L.marker([latitud, longitud], { icon: chooseIcon }).addTo(map);
        // Adjunta un popup al marcador con información del ejercicio
        marker.bindPopup("<b>Folio:</b> " + "<b>" + folio + "</b>" + "<br>" + "<b>Lona:</b> " + lona + "<br>" + "<b>Por:</b> " + name + "<br>" + fecha);

                        
        map.on('click', function(e) {
                    var latInput = document.querySelector('input[id$="latitud"]');
                    var lngInput = document.querySelector('input[id$="longitud"]');

                    if (latInput && lngInput) {
                        latInput.value = e.latlng.lat;
                        lngInput.value = e.latlng.lng;

                        latInput.dispatchEvent(new Event('input'));
                        lngInput.dispatchEvent(new Event('input'));
                    }

                    if (window.mapMarker) {
                        window.mapMarker.remove();
                    }

                    window.mapMarker = L.marker(e.latlng).addTo(map);
                });
    });
</script>
@endpush

