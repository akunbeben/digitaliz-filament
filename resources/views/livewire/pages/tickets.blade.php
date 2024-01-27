@php
    use App\Livewire\Widgets\TicketOverview;
@endphp

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tickets') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="flex flex-col mx-auto max-w-7xl sm:px-6 lg:px-8 gap-y-6">
            @livewire(TicketOverview::class, ['tickets' => $this->eloquentQuery()->get()])

            {{ $this->table }}
        </div>
    </div>
</div>
