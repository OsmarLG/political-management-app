<div>
    @if(App\Models\Encuesta::first())
    <form wire:submit="create">
        {{ $this->form }}
    </form>

    <div class="mt-[20px]">
        <x-filament::button wire:click="create">
            Guardar Ejercicio
        </x-filament::button>
    </div>


    <x-filament-actions::modals />

    @else
    <p>Se debe crear la Encuesta antes de comenzar.</p>
    @endif
</div>
