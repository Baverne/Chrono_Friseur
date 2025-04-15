<form
    {{ $attributes }}
    :id="mode + '-event-form'"
    x-data="{
        picker: null,
        init() {
            this.picker = flatpickr(this.$refs.picker, {
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: 'd/m/Y H:i',
                allowInput: true,
                enableTime: true,
                inline: true,
                locale: 'fr',
                onChange: (date, dateString) => {
                    this.formEvent.date = dateString
                }
            })
            this.$watch('formEvent', (event) => this.picker.setDate(event.date))
        },
        get label(){
            if(this.formEvent.tags.length === 0){
                return 'Choisir des tags...';
            }
            return this.formEvent.tags.length === 1 ? this.formEvent.tags[0].name.fr : `${this.formEvent.tags.length} sélectionnés`;
        }
    }"
    @submit.prevent="mode === 'editEvent' ? updateEvent() : addEvent()"
>
    <div class="flex flex-col my-2 space-y-3 max-w-2xl text-gray-500 border-b border-gray-900/10 pb-6">
        <!-- Name -->
        <div class="">
            <label for="name" class="block text-sm/6 font-medium">Nom</label>
            <div class="mt-2">
                <input x-model="formEvent.name" type="text" name="name" id="name"
                       class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                >
            </div>
            <template x-for="error in eventFormErrors.name" :key="error">
            <x-form-error x-text="error"/>
            </template>
        </div>

        <!-- Tags -->
        <div>
            <label for="tags" class="text-sm font-medium select-none">Tags</label>

            <div class="mt-2">
                <!-- Listbox -->
                <div x-listbox x-model="formEvent.tags" multiple by="id"
                     class="relative p-0 bg-transparent border-0"
                >
                    <!-- Label -->
                    <label x-listbox:label class="sr-only">Tags</label>

                    <!-- Button -->
                    <button x-listbox:button
                            class="group flex w-full items-center justify-between gap-2 rounded-md border border-gray-300 bg-white px-3 py-1.5 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                    >
                        <span x-text="label" class="truncate text-base text-gray-900 sm:text-sm/6"
                              :class="{ '!text-gray-400': $listbox.value.length !== 1 }"
                        ></span>

                        <x-icons.chevron-down class="shrink-0 text-gray-300 group-hover:text-gray-800" size="size-5"/>
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
                                'text-gray-400 cursor-not-allowed': $listboxOption.isDisabled,
                            }"
                        >
                            <div class="w-6 shrink-0">
                                <x-icons.check x-show="$listboxOption.isSelected" class="shrink-0" size="size-5"/>
                            </div>

                            <x-icons.solid-tag x-bind:style="`color: ${tag?.color ?? '#000000'}`" class="mr-1"
                                               size="size-4"
                            />

                            <span x-text="tag?.name.fr"></span>
                        </li>
                        </template>
                    </ul>
                </div>
            </div>

            <template x-for="error in eventFormErrors.tags" :key="error">
            <x-form-error x-text="error"/>
            </template>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm/6 font-medium">Description</label>

            <div class="mt-2">
                <textarea x-model="formEvent.description" id="description" name="description"
                          class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                ></textarea>
            </div>

            <template x-for="error in eventFormErrors.description" :key="error">
            <x-form-error x-text="error"/>
            </template>
        </div>

        <!-- Date -->
        <div>
            <label for="picker" class="text-sm font-medium select-none">Date</label>

            <div class="mt-2">
                <input id="picker" x-ref="picker" x-model="formEvent.date" name="date" type="text"
                       placeholder="01/01/2001 16:20"
                       class="block w-full rounded-md bg-white px-3 py-1.5 mb-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                >
            </div>

            <template x-for="error in eventFormErrors.date" :key="error">
            <x-form-error x-text="error"/>
            </template>
        </div>
    </div>
</form>
