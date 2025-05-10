<div {{ $attributes }}
     x-data="{
        showEditEvent() {
            this.mode = 'editEvent';
            if (this.formEvent.id !== this.selectedEvent.id) {
                var e = this.events.get(this.selectedEvent.id);
                this.formEvent = {
                    id: e.id,
                    name: e.name,
                    description: e.description,
                    date: e.date,
                    tags: e.tags
                };
                this.eventFormErrors = { name: [], description: [], date: [], tags: [] };
            }
        },
        preventEventDelete: true,
        deleteEvent() {
            // Must execute this action twice in 3 seconds to effectively delete.
            if (this.preventEventDelete) {
                this.preventEventDelete = false;
                setTimeout(() => {
                    this.preventEventDelete = true;
                }, 3000)
            } else {
                this.preventEventDelete = true;
                this.eventRequestInProgress = true;
                axios.delete('/events/' + this.selectedEvent.id)
                    .then(response => {
                        this.events.remove(this.selectedEvent.id);
                        this.selectedEvent = { id: null, name: null, description: null, date: null, tags: [] };
                        this.openEventFlyout = false;
                        this.$dispatch('notify', { content: response.data.message, type: 'success' })
                    }).catch(error => {
                        if (error.status === 404) {
                            this.$dispatch('notify', { content: `L'événement est introuvable.`, type: 'error' })
                        } else {
                            this.$dispatch('notify', { content: `Une erreur s'est produite lors de la suppression.`, type: 'error' })
                        }
                    }).finally(() => { this.eventRequestInProgress = false; })
            }
        }
    }"
>
    <div class="flex flex-col my-2 space-y-4 border-b border-gray-900/10 pb-6">
        <p x-text="selectedEvent.name" class="font-semibold text-lg"></p>

        <template x-if="selectedEvent.tags.length > 0">
        <div class="space-x-1">
            <template x-for="tag in selectedEvent.tags" :key="tag.id">
            <x-tags.badge x-bind:style="`fill: ${tags.get(tag.id)?.color ?? 'black'}`">
                <span x-text="tags.get(tag.id)?.name.fr"></span>
            </x-tags.badge>
            </template>
        </div>
        </template>

        <p x-cloak
           x-text="moment(selectedEvent.date).format('llll') + ' (' + moment(selectedEvent.date).fromNow() + ')'"
        ></p>

        <p x-text="selectedEvent.description" class="text-gray-500"></p>
    </div>

    <div class="mt-6 flex justify-end space-x-2">
        <button @click.prevent="showEditEvent()" type="button"
                class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent bg-transparent px-3 py-2 font-semibold text-gray-800 hover:bg-gray-800/10 transition"
        >
            <x-icons.pencil-square size="size-5"/>

            <span>Modifier</span>
        </button>

        <button @click.prevent="deleteEvent()" type="button"
                class="relative flex items-center justify-center space-x-1 whitespace-nowrap rounded-lg border border-transparent px-3 py-2 text-white font-semibold bg-red-600 outline-0 outline-transparent hover:bg-red-500 focus:outline-2 focus:outline-offset-2 focus:outline-red-700 transition"
                :class="{'opacity-50 cursor-not-allowed': eventRequestInProgress, 'animate-wiggle': !preventEventDelete}"
                :disabled="eventRequestInProgress"
        >
            <template x-if="preventEventDelete && !eventRequestInProgress">
            <x-icons.trash size="size-5"/>
            </template>

            <template x-if="eventRequestInProgress">
            <x-icons.spinner size="size-5"/>
            </template>

            <template x-if="!eventRequestInProgress && !preventEventDelete">
            <x-icons.face-frown size="size-5"/>
            </template>

            <span x-show="!eventRequestInProgress"
                  x-text="preventEventDelete ? 'Supprimer' : 'Vraiment ?'"
            ></span>
        </button>
    </div>
</div>
