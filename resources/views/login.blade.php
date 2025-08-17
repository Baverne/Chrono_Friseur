<x-layout>
    <x-slot:title>Connexion - Chrono Friseur</x-slot:title>

    <div class="relative bg-white bg-cover">
        <div
            class="absolute bg-gradient-to-bl from-slate-50 via-indigo-100 to-indigo-300 animate-[pulse_5s_ease-in-out_infinite] w-full h-full"
        ></div>

        <div class="absolute bg-dots animate-pulse w-full h-full"></div>

        <div class="flex flex-col items-center justify-center align-middle mx-4 min-h-screen z-10">
            <div class="drop-shadow-lg">
                <h1 class="text-4xl font-light text-gray-900">
                    <span class="font-extrabold">Chrono</span> Friseur
                </h1>
            </div>

            <a href="{{ route('auth.github') }}"
               class="flex items-center justify-center w-full mt-6 px-6 py-4 bg-white/40 backdrop-blur-xs shadow-lg overflow-hidden rounded-md hover:bg-white/60 hover:shadow-2xl focus:outline-2 sm:max-w-md transition z-20"
            >
                <div class="flex items-center space-x-2 mx-2 p-2">
                    <x-icons.github size="size-8"/>
                    <span class="font-bold">Connexion avec GitHub</span>
                </div>
            </a>

            @if(config('app.env') === 'local' && config('app.debug'))
                <form action="{{ route('dev.login') }}" method="POST"
                      class="flex items-center justify-center w-full mt-4 px-6 py-4 bg-orange-50/40 backdrop-blur-xs shadow-lg overflow-hidden rounded-md hover:bg-orange-50/60 hover:shadow-2xl focus:outline-2 sm:max-w-md transition z-20"
                >
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 mx-2 p-2">
                        <x-icons.log-in size="size-8"/>
                        <span class="font-bold text-orange-800">Connexion développeur</span>
                    </button>
                </form>
            @endif

            @if($errors->any())
                <div
                    class="flex w-full mt-6 p-4 opacity-100 rounded-md bg-red-50/50 backdrop-blur-xs shadow sm:max-w-md"
                >
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                 data-slot="icon"
                            >
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                                      clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Il y a eu {{ $errors->count() }} {{ $errors->count() > 1 ? 'erreurs' : 'erreur' }}
                                lors de la connexion.
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul role="list" class="list-disc space-y-1 pl-5 [&_a]:underline">
                                    @foreach($errors->all() as $error)
                                        <li>{!! Str::of($error)->markdown() !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
