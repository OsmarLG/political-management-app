<div>
    <h1>Mapa Barda Número: {{ $getRecord()->numero ?? '' }}</h1>
    <button type="button" id="activateMapBarda">Obtener Ubicaci&oacute;n</button>
    <div wire:ignore id="mapaBarda" style="height: 400px;"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var marker = null;
        var iconoBardaUrl = "{{ asset('images/icons/barda.png') }}";

        // Definir un icono personalizado
        var iconoBarda = L.icon({
            iconUrl: iconoBardaUrl, // Usa la función helper asset() de Laravel
            iconSize: [20, 20], // Tamaño del icono
            iconAnchor: [20, 20], // Punto del icono que corresponderá a la ubicación del marcador
            popupAnchor: [-3, -76] // Punto desde el que se abrirá el popup en relación al iconAnchor
        });

        var map = L.map('mapaBarda').setView([24.1353403, -110.2867958], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Inicializar la variable del marcador como null.
        var currentMarker = null;

        @if($getRecord() && $getRecord()->asignacionGeografica)

            var latitud = Number('{{ $getRecord()->asignacionGeografica->latitud }}');
            var longitud = Number('{{ $getRecord()->asignacionGeografica->longitud }}');

            currentMarker = L.marker([latitud, longitud], { icon: iconoBarda }).addTo(map);

            map.setView([latitud, longitud], 13);

        @endif

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // Verificar si ya existe un marcador.
            if (currentMarker) {
                // Mover el marcador existente a la nueva ubicación.
                currentMarker.setLatLng([lat, lng]);
            } else {
                // Crear un nuevo marcador y asignarlo a currentMarker.
                currentMarker = L.marker([lat, lng], { icon: iconoBarda }).addTo(map);
            }

            map.setView([lat, lng], 13);

            var latInput = document.querySelector('#data\\.Latitud');
            var lngInput = document.querySelector('#data\\.Longitud');

            if (latInput && lngInput) {
                latInput.value = lat;
                lngInput.value = lng;

                // Disparar un evento input para que Livewire se actualice con los nuevos valores
                latInput.dispatchEvent(new Event('input'));
                lngInput.dispatchEvent(new Event('input'));
            }
        });

        document.querySelector('#activateMapBarda').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    if (currentMarker) {
                        currentMarker.setLatLng([lat, lng]);
                    } else {
                        currentMarker = L.marker([lat, lng], { icon: iconoBarda }).addTo(map);
                    }

                    map.setView([lat, lng], 13);
                    var latInput = document.querySelector('#data\\.Latitud');
                    var lngInput = document.querySelector('#data\\.Longitud');

                    if (latInput && lngInput) {
                        latInput.value = lat;
                        lngInput.value = lng;

                        // Disparar un evento input para que Livewire se actualice con los nuevos valores
                        latInput.dispatchEvent(new Event('input'));
                        lngInput.dispatchEvent(new Event('input'));
                    }
                }, function(error) {
                    console.error('Error getting location:', error);
                });
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        });
    });
</script>
@endpush

