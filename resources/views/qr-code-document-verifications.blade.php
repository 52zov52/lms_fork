<x-guest-layout>
    <div class="mx-auto flex flex-col space-y-4 max-w-md px-2 md:px-0">
        <div class="mt-4 flex flex-col items-center justify-center">
            <a href="{{ route('login') }}">
                <img id="logo-pc-id" x-bind:src="logo" alt="" class="w-28">
            </a>
            <p class="text-2xl text-center font-semibold">{{ __('ЧОУ ВО ЮУ (ИУБиП)') }}</p>
            <p class="text-2xl text-center font-semibold">{{ __('первый инновационный') }}</p>
        </div>
        @livewire('qr-code-document-verifications', ['verificationCode' => $verificationCode])
    </div>
</x-guest-layout>