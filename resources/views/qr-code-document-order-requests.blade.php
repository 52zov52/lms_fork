<x-guest-layout>
    <div class="mx-auto flex flex-col space-y-4 max-w-md px-2 md:px-0">
        <!-- Logo -->
        <div class="mt-4 flex flex-col items-center justify-center">
            <a href="{{ route('login') }}">
                <img src="{{ asset('storage/logo.svg') }}" alt="logo">
            </a>
            <p class="text-2xl text-center font-semibold">{{ __('ИУБиП|Digital') }}</p>
        </div>
        <!-- Document Order Request Form -->
        @livewire('qr-code-document-order-requests')
    </div>
</x-guest-layout>