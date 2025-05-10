<div {{ $attributes->merge(['class' => "flex items-center justify-between"]) }}
     x-data="{
        showEditTag(tag) {
            this.mode = 'editTag';
            this.formTag = { id: tag.id, name: { fr: tag.name.fr }, color: tag.color };
            this.tagFormErrors = { name: [], color: [] };
        },
        deleteTag(tag) {
            // Must execute this action twice in 3 seconds to effectively delete.
            if (this.preventTagDelete[tag.id]) {
                this.preventTagDelete[tag.id] = false;
                setTimeout(() => {
                    this.preventTagDelete[tag.id] = true;
                }, 3000);
            } else {
                this.preventTagDelete[tag.id] = true;
                this.tagRequestInProgress[tag.id] = true;
                axios.delete('/tags/' + tag.id)
                    .then(response => {
                        this.tags.remove(tag.id);
                        // Update events which had this tag
                        if (response.data.affected_events) {
                            response.data.affected_events.forEach(e => {
                                this.events.updateOnly(this.makeEventData(e));
                            })
                        }
                        // Reset possible copies of an event which had this tag
                        this.selectedEvent = { id: null, name: null, description: null, date: null, tags: [] };
                        this.formEvent = { id: null, name: null, description: null, date: null, tags: [] };

                        this.$dispatch('notify', { content: response.data.message, type: 'success' });
                    }).catch(error => {
                        if (error.status === 404) {
                            this.$dispatch('notify', { content: `Le tag n'existe pas.`, type: 'error' });
                        } else {
                            this.$dispatch('notify', { content: `Une erreur s'est produite lors de la suppression.`, type: 'error' });
                        }
                    }).finally(() => {
                        delete this.preventTagDelete[tag.id];
                        delete this.tagRequestInProgress[tag.id];
                    })
            }
        }
    }"
>
    <div class="flex space-x-2 items-center">
        <x-icons.solid-tag size="size-5" x-bind:style="`fill: ${tag.color ?? 'black'}`"/>

        <p x-text="tag.name.fr" class="text-base"></p>
    </div>

    <div class="flex space-x-1 items-center">
        <button x-tooltip="'Modifier'" @click="showEditTag(tag)" type="button"
                class="relative flex items-center justify-center whitespace-nowrap rounded-full p-1.5 text-gray-800 text-sm outline-0 outline-transparent  hover:bg-gray-800/10 focus:outline-2 focus:outline-gray-600 transition"
        >
            <x-icons.pencil-square size="size-5"/>
        </button>

        <button x-tooltip="'Supprimer'" @click="deleteTag(tag)" type="button" :disabled="tagRequestInProgress[tag.id]"
                class="relative flex items-center justify-center whitespace-nowrap rounded-full p-1.5 text-red-500 text-sm outline-0 outline-transparent hover:text-white hover:bg-red-500 focus:outline-2 focus:outline-offset-2 focus:outline-red-600 transition"
                :class="{
                    'opacity-50 cursor-not-allowed': tagRequestInProgress[tag.id],
                     'animate-wiggle': !preventTagDelete[tag.id]
                 }"

        >
            <template x-if="preventTagDelete[tag.id] && !tagRequestInProgress[tag.id]">
            <x-icons.trash size="size-5"/>
            </template>

            <template x-if="!tagRequestInProgress[tag.id] && !preventTagDelete[tag.id]">
            <x-icons.face-frown size="size-5"/>
            </template>

            <span x-show="!tagRequestInProgress[tag.id] && !preventTagDelete[tag.id]">?</span>

            <template x-if="tagRequestInProgress[tag.id]">
            <x-icons.spinner size="size-5"/>
            </template>
        </button>
    </div>
</div>
