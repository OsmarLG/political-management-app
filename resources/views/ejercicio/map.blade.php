<h1>Mapa {{ $getRecord()->nombre ?? '' }}</h1>
<div wire:ignore id="mapaEjercicio" style=" width:100%; height: 400px;"></div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('mapaEjercicio').setView([24.1353403, -110.2867958], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        }).addTo(map);

    });
</script>
@endpush
