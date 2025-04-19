<div
    {{ $attributes->merge(['class' => 'fixed inset-0 overflow-hidden z-20']) }}
    x-dialog
    x-cloak
>
    <!-- Overlay -->
    <div x-dialog:overlay x-transition.opacity class="fixed inset-0 bg-black/25"></div>

    <!-- Panel -->
    <div class="fixed inset-y-0 right-0 max-w-lg w-full max-h-dvh min-h-dvh">
        <div
            x-dialog:panel
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="h-full w-full"
        >
            <div class="h-full flex flex-col bg-white shadow-lg overflow-y-auto p-8">
                <!-- Close Button -->
                <div class="absolute right-0 top-0 mr-4 mt-4">
                    <button @click="$dialog.close()" type="button" id="close-flyout"
                            class="relative inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md bg-transparent p-1.5 font-medium text-gray-400 hover:bg-gray-800/10 hover:text-gray-800 transition"
                    >
                        <span class="sr-only">Fermer le panneau</span>

                        <x-icons.cross size="size-5"/>
                    </button>
                </div>

                <!-- Body -->
                <div class="space-y-6">
                    <!-- Title -->
                    <h2 x-dialog:title class="font-medium text-gray-800 text-xl">
                        <span x-show="mode === 'showEvent'">Détails de l'événement</span>
                        <span x-show="mode === 'editEvent'">Modifier l'événement</span>
                        <span x-show="mode === 'addEvent'">Ajouter un événement</span>
                        <span x-show="mode === 'listTags' || mode === 'editTag' || mode === 'addTag'"
                        >Gestion des tags</span>
                        <span x-show="mode === 'search'">Rechercher & Filtrer par&mldr;</span>
                    </h2>

                    <template x-if="!selectedEvent.id && (mode === 'showEvent' || mode === 'editEvent')">
                    <!-- Loading event data -->
                    <div class="space-y-3 animate-pulse">
                        <div class="rounded-md bg-gray-200/70 h-5 w-[300px]"></div>
                        <div class="rounded-md bg-gray-200/70 h-5 w-[250px]"></div>
                        <div class="rounded-md bg-gray-200/70 h-5 w-[200px]"></div>
                    </div>
                    </template>


                    <!-- Content -->
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
