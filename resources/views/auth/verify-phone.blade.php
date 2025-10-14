<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Последний шаг! Подтвердите ваш номер телефона <strong>{{ auth()->user()->phone }}</strong>. Нажмите кнопку ниже, чтобы получить SMS с кодом.
    </div>

    @if (session('success'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-4 font-medium text-sm text-yellow-600">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.phone.verify') }}">
        @csrf

        <!-- Verification Code -->
        <div>
            <x-input-label for="code" value="Код подтверждения из SMS" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" maxlength="6" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">Код действителен в течение 10 минут</p>
        </div>

        <div class="flex items-center justify-between mt-4">
            <x-primary-button>
                Подтвердить
            </x-primary-button>

            <div class="flex flex-col items-end">
                <form method="POST" action="{{ route('verification.phone.send') }}" class="inline">
                    @csrf
                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Отправить SMS код
                    </button>
                </form>

                <form method="POST" action="{{ route('verification.phone.resend') }}" class="inline mt-2">
                    @csrf
                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Отправить код повторно
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="inline mt-2">
                    @csrf
                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </form>
</x-guest-layout>
