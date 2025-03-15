<form id="eventForm" class="bg-gray-100 rounded p-5 shadow">
    @csrf
    <input type="hidden" name="event-id" value="{{ $id ?? '' }}">
    <div class="flex flex-col items-center">
        <div class="w-full">
            <div class="flex flex-col max-w-2xl border-b border-gray-900/10 pb-6">
                <div class="row mb-2">
                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ $name ?? '' }}"
                               class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                    <div class="mt-2">
                            <textarea id="description" name="description"
                                      class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{ $description ?? '' }}</textarea>
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
                        }}" class="row">
                    <label for="picker" class="text-sm font-medium select-none text-gray-800">Date</label>
                    <div class="mt-2">
                        <input class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                               x-ref="picker" id="picker" type="text" name="date" value="{{ $date ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-2">
            <button type="button" class="text-sm/6 font-semibold text-gray-900">Annuler</button>
            <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <x-icons.check size="size-4"/>
            </button>
            <button type="button"
                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                    id="deleteEventButton">
                <x-icons.trash size="size-4"/>
            </button>
        </div>
    </div>
</form>
