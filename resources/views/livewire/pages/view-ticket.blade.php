<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Ticket &mdash; {{ $this->ticket->platform->getLabel() }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="flex flex-col mx-auto max-w-7xl sm:px-6 lg:px-8 gap-y-6">
            {{ $this->ticketInfolist }}
        </div>
    </div>
</div>
