<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        {{--
        <x-filament-panels::form.actions :actions="$this->getHeaderActions()" /> --}}
    </x-filament-panels::form>
</x-filament-panels::page>