<div>
    <form wire:submit="create">
        {{ $this->form }}
    </form>

    <div class="mt-[20px]">
        <x-filament::button wire:click="create">
            Guardar Ejercicio
        </x-filament::button>
    </div>


    <x-filament-actions::modals />
</div>
