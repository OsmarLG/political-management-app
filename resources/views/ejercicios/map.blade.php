<div>
    <h1>Mapa</h1>
    <button type="button" id="activateMapEjercicio">Obtener Ubicaci&oacute;n</button>
    <div wire:ignore id="mapaEjercicio" style="height: 400px;"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('mapaEjercicio').setView([24.1353403, -110.2867958], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        }).addTo(map);
        window.mapInstance = map;

        function getLocationAndUpdateMap() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const { latitude, longitude } = position.coords;
                    updateLatLngInputs(latitude, longitude);
                    if (!window.mapInitialized) {
                        initMap(latitude, longitude); // Inicializa el mapa con la ubicación actual
                    } else {
                        updateMap(latitude, longitude); // Actualiza el mapa con la ubicación actual
                    }
                    // Asegúrate de que el marcador se coloca en la ubicación obtenida.
                    placeMarker(latitude, longitude);
                }, function(error) {
                    console.error('Error getting location:', error);
                });
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        }

        function updateMap(latitude, longitude) {
            if (window.map) {
                window.map.setView([latitude, longitude], 13);
            }
        }

        function initMap(latitude = 24.1353403, longitude = -110.2867958) {
            window.mapInstance.remove();
            map = L.map('mapaEjercicio').setView([latitude, longitude], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            }).addTo(map);
            map.on('click', function(e) {
                updateLatLngInputs(e.latlng.lat, e.latlng.lng);
                // Coloca un marcador cada vez que se hace clic en el mapa.
                placeMarker(e.latlng.lat, e.latlng.lng);
            });

            window.map = map; // Guarda el mapa en window.map para acceso global
            window.mapInitialized = true;
        }

        function placeMarker(lat, lng) {
            if (window.marker) {
                window.map.removeLayer(window.marker); // Elimina el marcador anterior si existe
            }
            window.marker = L.marker([lat, lng]).addTo(window.map); // Añade un nuevo marcador
        }

        function updateLatLngInputs(lat, lng) {
            var latInput = document.querySelector('#data\\.Latitud');
            var lngInput = document.querySelector('#data\\.Longitud');

            if (latInput && lngInput) {
                latInput.value = lat;
                lngInput.value = lng;

                // Disparar un evento input para que Livewire se actualice con los nuevos valores
                latInput.dispatchEvent(new Event('input'));
                lngInput.dispatchEvent(new Event('input'));
            }
        }

        document.addEventListener('click', function(event) {
            var activateMapBtn = event.target.closest('#activateMapEjercicio');

            if (activateMapBtn) {
                var mapContainer = document.querySelector('#mapaEjercicio');
                getLocationAndUpdateMap(); // Obtén la ubicación actual y actualiza el mapa
            }
        });
    });

</script>
@endpush