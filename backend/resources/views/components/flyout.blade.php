<div
    {{ $attributes->merge(['class' => 'fixed inset-0 overflow-hidden z-20']) }}
    x-dialog
    x-cloak>
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
            class="h-full w-full">
            <div class="h-full flex flex-col bg-white shadow-lg overflow-y-auto p-8">
                <!-- Close Button -->
                <div class="absolute right-0 top-0 mr-4 mt-4">
                    <button @click="$dialog.close()" type="button"
                            class="relative inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md bg-transparent p-1.5 font-medium text-gray-400 hover:bg-gray-800/10 hover:text-gray-800"
                    >
                        <span class="sr-only">Fermer le panneau</span>
                        <x-icons.cross size="size-5"/>
                    </button>
                </div>

                <!-- Body -->
                <div class="space-y-6">
                    <!-- Title -->
                    <h2 x-dialog:title class="font-medium text-gray-800 text-xl">
                        <span x-show="mode === 'show'">Détails de l'événement</span> <span x-show="mode === 'edit'">Modifier l'événement</span>
                        <span x-show="mode === 'add'">Ajouter un événement</span>
                    </h2>

                    <template x-if="!currentEvent.id && mode !== 'add'">
                        <!-- Loading event data -->
                        <div class="space-y-3 animate-pulse">
                            <div class="rounded-md bg-gray-200/70 h-5 w-[300px]"></div>
                            <div class="rounded-md bg-gray-200/70 h-5 w-[250px]"></div>
                            <div class="rounded-md bg-gray-200/70 h-5 w-[200px]"></div>
                        </div>
                    </template>

                    <!-- Content -->
                    {{ $slot }}

                    <!-- Actions for show -->
                    <div x-show="mode === 'show'" class="mt-6 flex justify-end space-x-2">
                        <button
                            @click.prevent="mode = 'edit'"
                            type="button"
                            class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent bg-transparent px-3 py-2 font-semibold text-gray-800 hover:bg-gray-800/10"
                        >
                            <x-icons.pencil-square size="size-5"/>
                            <span>Modifier</span>
                        </button>

                        <button
                            @click.prevent="deleteEvent()"
                            type="button"
                            class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent px-3 py-2 text-white font-semibold bg-red-600 hover:bg-red-500"
                            :class="{'opacity-50 cursor-not-allowed': requestInProgress, 'animate-wiggle': !preventDelete}"
                            :disabled="requestInProgress"
                        >
                            <template x-if="preventDelete && !requestInProgress">
                                <x-icons.trash size="size-5"/>
                            </template>

                            <template x-if="requestInProgress">
                                <x-icons.spinner size="size-5"/>
                            </template>

                            <span
                                x-show="!requestInProgress"
                                x-text="preventDelete ? 'Supprimer' : 'Vraiment ? :('"
                            ></span>
                        </button>
                    </div>

                    <!-- Actions for edit -->
                    <div x-show="mode === 'edit'" class="mt-6 flex justify-end space-x-2">
                        <button
                            @click.prevent="mode = 'show'"
                            type="button"
                            class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent bg-transparent px-3 py-2 font-semibold text-gray-800 hover:bg-gray-800/10"
                        >
                            <span>Annuler</span>
                        </button>

                        <button
                            @click.prevent="updateEvent()"
                            type="button"
                            class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent px-3 py-2 text-white font-semibold bg-indigo-600 hover:bg-indigo-500"
                            :class="{'opacity-50 cursor-not-allowed': requestInProgress}"
                            :disabled="requestInProgress"
                        >
                            <template x-if="!requestInProgress">
                                <x-icons.pencil-square size="size-5"/>
                            </template>

                            <template x-if="requestInProgress">
                                <x-icons.spinner size="size-5"/>
                            </template>

                            <span>Appliquer</span>
                        </button>
                    </div>

                    <!-- Actions for add -->
                    <div x-show="mode === 'add'" class="mt-6 flex justify-end space-x-2">
                        <button
                            @click.prevent="$dialog.close()"
                            type="button"
                            class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent bg-transparent px-3 py-2 font-semibold text-gray-800 hover:bg-gray-800/10"
                        >
                            <span>Annuler</span>
                        </button>

                        <button
                            @click.prevent="addEvent()"
                            type="button"
                            class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent px-3 py-2 text-white font-semibold bg-indigo-600 hover:bg-indigo-500"
                            :class="{'opacity-50 cursor-not-allowed': requestInProgress}"
                            :disabled="requestInProgress"
                        >
                            <template x-if="!requestInProgress">
                                <x-icons.plus size="size-5"/>
                            </template>

                            <template x-if="requestInProgress">
                                <x-icons.spinner size="size-5"/>
                            </template>

                            <span>Ajouter</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
