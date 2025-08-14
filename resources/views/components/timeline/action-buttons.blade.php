<!-- List tags button -->
<button x-tooltip="'Gérer les tags'"
        @click="$el.blur(); $dispatch('list-tags')" type="button"
        class="text-sm font-semibold whitespace-nowrap h-min rounded-full p-3 bg-indigo-50 text-indigo-500 shadow hover:shadow-xl hover:bg-white outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 md:text-base md:bg-transparent md:shadow-none md:hover:shadow-none transition"
>
    <x-icons.solid-tag size="size-5 md:size-6"/>
</button>

<!-- Open search button -->
<button x-tooltip="'Filtrer les événements'"
        @click="$el.blur(); $dispatch('open-search')" type="button"
        class="text-sm font-semibold whitespace-nowrap h-min rounded-full p-3 bg-indigo-50 text-indigo-500 shadow hover:shadow-xl hover:bg-white outline-0 outline-transparent focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 md:text-base md:bg-transparent md:shadow-none md:hover:shadow-none transition"
>
    <x-icons.solid-funnel size="size-5 md:size-6"/>
</button>
