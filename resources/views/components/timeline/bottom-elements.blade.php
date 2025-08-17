<div class="flex justify-between space-x-4 w-full md:max-w-2xl md:mx-auto">
    <!-- Reset view button -->
    <div class="flex space-x-4 items-center">
        <button x-tooltip="'Ajuster la vue'"
                @click="timeline.fit();" type="button"
                class="text-sm font-semibold whitespace-nowrap h-min rounded-full p-3 bg-indigo-50 text-indigo-500 shadow hover:shadow-xl hover:bg-white outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 md:text-base md:bg-indigo-50/40 md:backdrop-blur-xs md:shadow md:hover:shadow-xl transition"
        >
            <x-icons.fit-view size="size-5 md:size-6"/>
        </button>

    </div>

    <!-- Dock elements -->
    <div
        class="flex justify-between w-full md:max-w-2xl md:rounded-full md:p-3 md:bg-indigo-50/40 md:backdrop-blur-xs md:shadow md:hover:shadow-xl md:transition"
    >
        <!-- Left elements -->
        <div class="flex-row items-center">
            <!-- User log-in button (should not be needed) -->
            @guest
                <button x-tooltip="'Se déconnecter'" @click="window.location.href = '{{ route('login') }}'"
                        class="text-sm font-semibold whitespace-nowrap h-min rounded-full p-3 bg-indigo-50 text-indigo-500 shadow hover:shadow-xl hover:bg-white outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 md:text-base md:bg-transparent md:shadow-none md:hover:shadow-none transition"
                >
                    <x-icons.log-in size="size-5 md:size-6"/>
                </button>
            @endguest

            <!-- User logout button -->
            @auth
                <button x-tooltip="'Se déconnecter'" @click="window.location.href = '{{ route('logout') }}'"
                        class="text-sm font-semibold whitespace-nowrap h-min rounded-full p-3 bg-indigo-50 text-indigo-500 shadow hover:shadow-xl hover:bg-white outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 md:text-base md:bg-transparent md:shadow-none md:hover:shadow-none transition"
                >
                    <x-icons.logout size="size-5 md:size-6"/>
                </button>
            @endauth
        </div>

        <!-- Right elements -->
        <div class="flex space-x-4 items-center">
            <!-- Actions (mobile) -->
            <div x-data x-popover class="relative sm:hidden">
                <!-- Other actions button -->
                <button x-popover:button x-tooltip="'Autres actions'" type="button"
                        class="text-sm font-semibold whitespace-nowrap h-min rounded-full p-3 bg-indigo-50 text-indigo-500 shadow hover:shadow-xl hover:bg-white outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 md:text-base md:bg-transparent md:shadow-none md:hover:shadow-none transition"
                >
                    <x-icons.ellipsis size="size-5 md:size-6"/>
                </button>

                <div x-popover:panel x-transition.origin.bottom x-cloak
                     class="absolute bottom-0 mb-13 origin-bottom flex-col space-y-2 items-baseline"
                >
                    <x-timeline.action-buttons/>
                </div>
            </div>

            <!-- Actions (desktop) -->
            <div class="hidden sm:flex justify-between space-x-4 items-center">
                <x-timeline.action-buttons/>
            </div>

            <!-- Add event button -->
            <button x-tooltip="'Ajouter un événement'"
                    @click="$el.blur(); $dispatch('add-event')" type="button"
                    class="text-sm font-semibold flex items-center justify-center whitespace-nowrap h-min rounded-full p-3 bg-indigo-600 text-white shadow hover:shadow-xl hover:bg-indigo-500 outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 md:text-base md:shadow-none md:hover:shadow-none transition"
            >
                <x-icons.plus size="size-5 md:size-6"/>

                <span class="hidden sm:inline" aria-hidden="true">&nbsp;Événement</span>
            </button>

            <!-- CSV Import button -->
            <button x-tooltip="'Importer un CSV'"
                    @click="$el.blur(); $dispatch('open-csv-import')" type="button"
                    class="text-sm font-semibold flex items-center justify-center whitespace-nowrap h-min rounded-full p-3 bg-green-600 text-white shadow hover:shadow-xl hover:bg-green-500 outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-green-700 md:text-base md:shadow-none md:hover:shadow-none transition"
            >
                <x-icons.document-arrow-up size="size-5 md:size-6"/>

                <span class="hidden sm:inline" aria-hidden="true">&nbsp;CSV</span>
            </button>
        </div>
    </div>
</div>
