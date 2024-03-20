<h1>Mapa {{ $getRecord()->nombre ?? '' }}</h1>
<div wire:ignore id="mapaZona" style=" width:100%; height: 400px;"></div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('mapaZona').setView([24.1353403, -110.2867958], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        }).addTo(map);

        @if($getRecord())
            var points = @json($getRecord()->asignacionesGeograficas->map(function ($asignacion) {
                return [$asignacion->latitud, $asignacion->longitud];
            }));

            if (points.length > 0) {
                points.push(points[0]); // Cierra el polígono
                var latlngs = points.map(function (point) { return [point[0], point[1]]; });
                var polygon = L.polygon(latlngs, { color: '#009EF3', fillColor: '#06DBF9', fillOpacity: 0.1 }).addTo(map);
                map.fitBounds(polygon.getBounds());

                // Calcula el centro del polígono para colocar la etiqueta
                var center = polygon.getBounds().getCenter();

                // Crea un marcador invisible en el centro del polígono
                var marker = L.marker(center, {opacity: 0}).addTo(map);

                // Adjunta un tooltip al marcador
                marker.bindTooltip("{{ $getRecord()->nombre }}", {
                    permanent: true, // Hace que el tooltip sea siempre visible
                    className: 'custom-tooltip', // Clase para estilos personalizados
                    offset: [0, 0], // Centra el tooltip en el marcador
                    direction: 'center' // Asegura que el tooltip se muestre en el centro
                });
            }
        @endif
    });
</script>
@endpush

