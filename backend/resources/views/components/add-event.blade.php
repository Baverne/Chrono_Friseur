<!-- AddEvent -->
<div
    x-data="{ openAddEvent: false }"
    {{--    @add-event="eventApiCall($event.detail)"--}}
    class="absolute bottom-12 right-12 z-10">
    <!-- AddEvent Button -->
    <button
        x-on:click="openAddEvent = true"
        type="button"
        class="inline-flex items-center space-x-1 rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
    >
        <x-icons.plus size="size-5"/>

        <span>Événement</span>
    </button>

    <!-- AddEvent Modal -->
    <div x-dialog x-model="openAddEvent" x-cloak class="fixed inset-0 z-10 overflow-y-auto">
        <!-- Overlay -->
        <div x-dialog:overlay x-transition.opacity class="fixed inset-0 bg-black/25"></div>

        <!-- Panel -->
        <div class="relative flex min-h-screen items-center justify-center p-4">
            <div x-dialog:panel x-transition class="relative min-w-96 max-w-xl rounded-xl bg-white p-6 shadow-lg">
                <!-- Close Button -->
                <div class="absolute right-0 top-0 mr-4 mt-4">
                    <button type="button" @click="$dialog.close()"
                            class="relative inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md bg-transparent p-1.5 font-medium text-gray-400 hover:bg-gray-800/10 hover:text-gray-800">
                        <span class="sr-only">Fermer la pop-up</span>
                        <x-icons.cross size="size-5"/>
                    </button>
                </div>

                <form
                    x-data="{
                        name: '{{ $name ?? '' }}',
                        description: '{{ $description ?? '' }}',
                        date: '{{ $date ?? '' }}',
                        submit() {
                            axios.post('/events', {
                                name: this.name,
                                description: this.description,
                                date: this.date
                            }).then(response => {
                                this.$dispatch('notify', response.data)
                                this.$dialog.close()
                                this.name= ''
                                this.description= ''
                                this.date= ''
                            }).catch(error => {
                                this.$dispatch('notify', error)
                            })
                        }
                    }"
                    x-on:submit.prevent="submit()"
                >
                    <!-- Body -->
                    <div>
                        <!-- Title -->
                        <h2 x-dialog:title class="font-medium text-gray-800">Ajouter un événement</h2>

                        <!-- Content -->
                        <div class="flex flex-col mt-2 max-w-2xl text-gray-500 border-b border-gray-900/10 pb-6">
                            <div class="row mb-2">
                                <label for="name" class="block text-sm/6 font-medium">Nom</label>
                                <div class="mt-2">
                                    <input x-model="name" type="text" name="name" id="name"
                                           class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="description" class="block text-sm/6 font-medium">Description</label>
                                <div class="mt-2">
                            <textarea x-model="description" id="description" name="description"
                                      class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            ></textarea>
                                </div>
                            </div>
                            <div x-data="{
                        value: '{{ $date ?? '' }}',
                        init() {
                            let picker = flatpickr(this.$refs.picker, {
                                dateFormat: 'd/m/Y h:i',
                                defaultDate: this.value,
                                allowInput: true,
                                enableTime: true,
                                onChange: (date, dateString) => {
                                    this.value = dateString
                                }
                            })
                            this.$watch('value', () => picker.setDate(this.value))
                        }}"
                                 class="row">
                                <label for="picker" class="text-sm font-medium select-none">Date</label>
                                <div class="mt-2">
                                    <input
                                        x-ref="picker" x-model="date" id="picker" type="text" name="date"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" x-on:click="$dialog.close()"
                                class="relative flex items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-transparent bg-transparent px-3 py-2 font-semibold text-sm text-gray-800 hover:bg-gray-800/10"
                        >Annuler
                        </button>

                        <button type="submit"
                                class="relative flex items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-transparent px-3 py-2 text-white font-semibold text-sm bg-indigo-600 hover:bg-indigo-500"
                        >Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
