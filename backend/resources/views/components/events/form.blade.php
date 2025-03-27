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
                    this.currentEvent.date = dateString
                }
            })
            this.$watch('currentEvent', (event) => this.picker.setDate(event.date))
        }
    }"
    @submit.prevent="mode === 'edit' ? updateEvent() : addEvent()"
>
    <div class="flex flex-col mt-2 max-w-2xl text-gray-500 border-b border-gray-900/10 pb-6">
        <!-- Name -->
        <div class="row mb-2">
            <label for="name" class="block text-sm/6 font-medium">Nom</label>
            <div class="mt-2">
                <input x-model="currentEvent.name" type="text" name="name" id="name"
                       class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
            </div>
            <template x-for="error in formErrors.name" :key="error">
                <x-form-error x-text="error"/>
            </template>
        </div>

        <!-- Description -->
        <div class="row mb-2">
            <label for="description" class="block text-sm/6 font-medium">Description</label>
            <div class="mt-2">
                <textarea x-model="currentEvent.description" id="description" name="description"
                          class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                ></textarea>
            </div>
            <template x-for="error in formErrors.description" :key="error">
                <x-form-error x-text="error"/>
            </template>
        </div>

        <!-- Date -->
        <div class="row">
            <label for="picker" class="text-sm font-medium select-none">Date</label>
            <div class="mt-2">
                <input x-ref="picker" x-model="currentEvent.date" id="picker" type="text" name="date"
                       placeholder="01/01/2001 16:20"
                       class="block w-full rounded-md bg-white px-3 py-1.5 mb-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                >
            </div>
            <template x-for="error in formErrors.date" :key="error">
                <x-form-error x-text="error"/>
            </template>
        </div>
    </div>
</form>
