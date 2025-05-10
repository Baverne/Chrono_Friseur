<div {{ $attributes }}
     x-data="{
        formSearch: {
            fulltext: '',
            with_tags: [],
            without_tags: [],
            after: '',
            before: ''
        },
        searchFormErrors: {
            fulltext: [],
            with_tags: [],
            without_tags: [],
            after: [],
            before: []
        },
        resetSearch() {
            this.formSearch = {
                fulltext: '',
                with_tags: [],
                without_tags: [],
                after: '',
                before: ''
            }
            this.searchFormErrors = {
                fulltext: [],
                with_tags: [],
                without_tags: [],
                after: [],
                before: []
            }
        },
        applyFilters() {
            this.eventRequestInProgress = true;
            this.getEvents(this.formSearch); // If form errors, dispatches 'events-errored' event with errors.
        }
     }"
     @events-loaded="openSearchFlyout = false;"
     @events-errored="searchFormErrors = $event.detail.errors"
>
    <form
        x-data="{
            pickerAfter: null,
            pickerBefore: null,
            init() {
                this.pickerAfter = flatpickr(this.$refs.pickerAfter, {
                    dateFormat: 'Y-m-d H:i',
                    altInput: true,
                    altFormat: 'd/m/Y H:i',
                    allowInput: true,
                    enableTime: true,
                    inline: true,
                    locale: 'fr',
                    onChange: (date, dateString) => {
                        this.formSearch.after = dateString
                    }
                })
                this.$watch('formSearch.after', (value) => this.pickerAfter.setDate(value))
                this.pickerBefore = flatpickr(this.$refs.pickerBefore, {
                    dateFormat: 'Y-m-d H:i',
                    altInput: true,
                    altFormat: 'd/m/Y H:i',
                    allowInput: true,
                    enableTime: true,
                    inline: true,
                    locale: 'fr',
                    onChange: (date, dateString) => {
                        this.formSearch.before = dateString
                    }
                })
                this.$watch('formSearch.before', (value) => this.pickerBefore.setDate(value))
            },
            get withTagsLabel(){
                if(this.formSearch.with_tags.length === 0){
                    return 'Choisir des tags...';
                }
                return this.formSearch.with_tags.length === 1 ? this.formSearch.with_tags[0].name.fr : `${this.formSearch.with_tags.length} sélectionnés`;
            },
            get withoutTagsLabel(){
                if(this.formSearch.without_tags.length === 0){
                    return 'Choisir des tags...';
                }
                return this.formSearch.without_tags.length === 1 ? this.formSearch.without_tags[0].name.fr : `${this.formSearch.without_tags.length} sélectionnés`;
            }
        }"
        :id="mode + '-event-form'"
        @submit.prevent="applyFilters()"
    >
        <div class="flex flex-col my-2 space-y-3 max-w-2xl text-gray-500 border-b border-gray-900/10 mb-4 pb-6">
            <div x-disclosure default-open
                 class="block border-b border-gray-800/10 pb-4 pt-4 space-y-2 first:pt-0 last:border-b-0 last:pb-0"
            >
                <button x-disclosure:button type="button"
                        class="group flex w-full items-center justify-between text-left font-medium text-gray-800 gap-2"
                >
                    <x-icons.chevron-down x-cloak x-bind:class="$disclosure.isOpen && '-rotate-180'" size="size-5"
                                          class="shrink-0 text-gray-300 group-hover:text-gray-800 transition-transform"
                    />

                    <span class="flex-1">Texte</span>
                </button>

                <div x-disclosure:panel x-collapse class="space-y-3">
                    <!-- Fulltext search on events (name, description) -->
                    <div>
                        <label for="fulltext" class="block text-sm/6 font-medium">Contient les mots...</label>
                        <div class="mt-2">
                            <input x-model="formSearch.fulltext" type="text" name="fulltext" id="fulltext"
                                   placeholder="Saisir des mots-clés&mldr;"
                                   class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            >
                        </div>
                        <template x-for="error in searchFormErrors.fulltext" :key="error">
                        <x-form-error x-text="error"/>
                        </template>
                    </div>
                </div>
            </div>

            <div x-disclosure default-open
                 class="block border-b border-gray-800/10 pb-4 pt-4 space-y-2 first:pt-0 last:border-b-0 last:pb-0"
            >
                <button x-disclosure:button type="button"
                        class="group flex w-full items-center justify-between text-left font-medium text-gray-800 gap-2"
                >
                    <x-icons.chevron-down x-cloak x-bind:class="$disclosure.isOpen && '-rotate-180'" size="size-5"
                                          class="shrink-0 text-gray-300 group-hover:text-gray-800 transition-transform"
                    />

                    <span class="flex-1">Tags</span>
                </button>

                <div x-disclosure:panel x-collapse class="space-y-3">
                    <!-- Must have tags -->
                    <div>
                        <label for="with_tags" class="text-sm font-medium select-none">A un des tags&mldr;</label>

                        <div class="mt-2">
                            <!-- Listbox -->
                            <div x-listbox x-model="formSearch.with_tags" multiple by="id"
                                 class="relative p-0 bg-transparent border-0"
                            >
                                <!-- Label -->
                                <label x-listbox:label class="sr-only">A un des tags&mldr;</label>

                                <!-- Button -->
                                <button x-listbox:button
                                        class="group flex w-full items-center justify-between gap-2 rounded-md border border-gray-300 bg-white px-3 py-1.5 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                                >
                                <span x-text="withTagsLabel" class="truncate text-base text-gray-900 sm:text-sm/6"
                                      :class="{ '!text-gray-400': $listbox.value.length !== 1 }"
                                ></span>

                                    <x-icons.chevron-down class="shrink-0 text-gray-300 group-hover:text-gray-800"
                                                          size="size-5"
                                    />
                                </button>

                                <!-- Options -->
                                <ul x-listbox:options x-cloak
                                    class="absolute right-0 z-10 mt-2 max-h-80 w-full overflow-y-scroll overscroll-contain rounded-md border border-gray-300 bg-white p-1.5 shadow-sm outline-none"
                                >
                                    <template x-if="tags.length === 0">
                                    <li class="text-gray-400 cursor-not-allowed group flex w-full items-center rounded-md px-2 py-1.5 transition-colors">
                                        <div class="w-6 shrink-0">
                                            <x-icons.face-frown size="size-5" class="shrink-0"/>
                                        </div>

                                        <span>Pas de tags</span>
                                    </li>
                                    </template>

                                    <template x-for="tag of tags.get({fields: ['id', 'color', 'name']})" :key="tag.id">
                                    <!-- Option -->
                                    <li x-listbox:option :value="tag" :disabled="false"
                                        class="group flex w-full cursor-default items-center rounded-md px-2 py-1.5 transition-colors"
                                        :class="{
                                        'bg-gray-100': $listboxOption.isActive,
                                        'text-gray-900': ! $listboxOption.isActive && ! $listboxOption.isDisabled,
                                        'text-gray-400 cursor-not-allowed': $listboxOption.isDisabled
                                    }"
                                    >
                                        <div class="w-6 shrink-0">
                                            <x-icons.check x-show="$listboxOption.isSelected" class="shrink-0"
                                                           size="size-5"
                                            />
                                        </div>

                                        <x-icons.solid-tag x-bind:style="`color: ${tag?.color ?? '#000000'}`"
                                                           class="mr-1"
                                                           size="size-4"
                                        />

                                        <span x-text="tag?.name.fr"></span>
                                    </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <template x-for="error in searchFormErrors.with_tags" :key="error">
                        <x-form-error x-text="error"/>
                        </template>
                    </div>

                    <!-- Must not have tags -->
                    <div>
                        <label for="without_tags" class="text-sm font-medium select-none">N&apos;a pas les
                            tags&mldr;</label>

                        <div class="mt-2">
                            <!-- Listbox -->
                            <div x-listbox x-model="formSearch.without_tags" multiple by="id"
                                 class="relative p-0 bg-transparent border-0"
                            >
                                <!-- Label -->
                                <label x-listbox:label class="sr-only">N&apos;a pas les tags&mldr;</label>

                                <!-- Button -->
                                <button x-listbox:button
                                        class="group flex w-full items-center justify-between gap-2 rounded-md border border-gray-300 bg-white px-3 py-1.5 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                                >
                                <span x-text="withoutTagsLabel" class="truncate text-base text-gray-900 sm:text-sm/6"
                                      :class="{ '!text-gray-400': $listbox.value.length !== 1 }"
                                ></span>

                                    <x-icons.chevron-down class="shrink-0 text-gray-300 group-hover:text-gray-800"
                                                          size="size-5"
                                    />
                                </button>

                                <!-- Options -->
                                <ul x-listbox:options x-cloak
                                    class="absolute right-0 z-10 mt-2 max-h-80 w-full overflow-y-scroll overscroll-contain rounded-md border border-gray-300 bg-white p-1.5 shadow-sm outline-none"
                                >
                                    <template x-if="tags.length === 0">
                                    <li class="text-gray-400 cursor-not-allowed group flex w-full items-center rounded-md px-2 py-1.5 transition-colors">
                                        <div class="w-6 shrink-0">
                                            <x-icons.face-frown size="size-5" class="shrink-0"/>
                                        </div>

                                        <span>Pas de tags</span>
                                    </li>
                                    </template>

                                    <template x-for="tag of tags.get({fields: ['id', 'color', 'name']})" :key="tag.id">
                                    <!-- Option -->
                                    <li x-listbox:option :value="tag" :disabled="false"
                                        class="group flex w-full cursor-default items-center rounded-md px-2 py-1.5 transition-colors"
                                        :class="{
                                        'bg-gray-100': $listboxOption.isActive,
                                        'text-gray-900': ! $listboxOption.isActive && ! $listboxOption.isDisabled,
                                        'text-gray-400 cursor-not-allowed': $listboxOption.isDisabled
                                    }"
                                    >
                                        <div class="w-6 shrink-0">
                                            <x-icons.check x-show="$listboxOption.isSelected" class="shrink-0"
                                                           size="size-5"
                                            />
                                        </div>

                                        <x-icons.solid-tag x-bind:style="`color: ${tag?.color ?? '#000000'}`"
                                                           class="mr-1"
                                                           size="size-4"
                                        />

                                        <span x-text="tag?.name.fr"></span>
                                    </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <template x-for="error in searchFormErrors.without_tags" :key="error">
                        <x-form-error x-text="error"/>
                        </template>
                    </div>
                </div>
            </div>


            <div x-disclosure
                 class="block border-b border-gray-800/10 pb-4 pt-4 space-y-2 first:pt-0 last:border-b-0 last:pb-0"
            >
                <button x-disclosure:button type="button"
                        class="group flex w-full items-center justify-between text-left font-medium text-gray-800 gap-2"
                >
                    <x-icons.chevron-down x-cloak x-bind:class="$disclosure.isOpen && '-rotate-180'" size="size-5"
                                          class="shrink-0 text-gray-300 group-hover:text-gray-800 transition-transform"
                    />

                    <span class="flex-1">Dates</span>
                </button>

                <div x-disclosure:panel x-collapse class="space-y-3">
                    <!-- Après la date -->
                    <div>
                        <label for="pickerAfter" class="text-sm font-medium select-none">Après&mldr;</label>

                        <div class="mt-2">
                            <input id="pickerAfter" x-ref="pickerAfter" x-model="formSearch.after" name="after"
                                   type="text"
                                   placeholder="01/01/1901 01:00"
                                   class="block w-full rounded-md bg-white px-3 py-1.5 mb-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            >
                        </div>

                        <template x-for="error in searchFormErrors.after" :key="error">
                        <x-form-error x-text="error"/>
                        </template>
                    </div>

                    <!-- Avant la date -->
                    <div>
                        <label for="pickerBefore" class="text-sm font-medium select-none">Avant&mldr;</label>

                        <div class="mt-2">
                            <input id="pickerBefore" x-ref="pickerBefore" x-model="formSearch.before" name="before"
                                   type="text"
                                   placeholder="01/01/2001 01:00"
                                   class="block w-full rounded-md bg-white px-3 py-1.5 mb-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            >
                        </div>

                        <template x-for="error in searchFormErrors.before" :key="error">
                        <x-form-error x-text="error"/>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions for search -->
        <div class="mt-6 flex justify-between items-center space-x-2">
            <button x-tooltip="'Réinitialiser'" @click.prevent="resetSearch()" type="button"
                    class="relative flex items-center justify-center whitespace-nowrap h-min rounded-full p-1.5 text-gray-500 text-sm outline-0 outline-transparent  hover:text-gray-800 hover:bg-gray-800/10 focus:outline-2 focus:outline-gray-600 transition"
            >
                <x-icons.back size="size-5"/>
            </button>

            <div class="flex justify-end space-x-2">
                <button @click.prevent="$dialog.close()" type="button"
                        class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent bg-transparent px-3 py-2 font-semibold text-gray-800 hover:bg-gray-800/10 focus:outline-2 focus:outline-gray-600 transition"
                ><span>Annuler</span></button>

                <button @click.prevent="applyFilters()" type="submit"
                        class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent px-3 py-2 text-white font-semibold bg-indigo-600 outline-0 outline-transparent hover:bg-indigo-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-700 transition"
                        :class="{'opacity-50 cursor-not-allowed': eventRequestInProgress}"
                        :disabled="eventRequestInProgress"
                >
                    <template x-if="!eventRequestInProgress">
                    <x-icons.solid-funnel size="size-5"/>
                    </template>

                    <template x-if="eventRequestInProgress">
                    <x-icons.spinner size="size-5"/>
                    </template>

                    <span>Appliquer</span>
                </button>
            </div>
        </div>
    </form>
</div>
