<div
    x-data="{
        loading: true,
        events: [],
        tags: [],
        timeline: null,
        options: {
            start: '1020-01-01',
            end: '2030-12-31',
            onInitialDrawComplete: null,
            margin: {
                item: 20
            },
            locale: 'fr'
        },
        init() {
            this.options.onInitialDrawComplete = () => { this.loading = false };
            this.events = new DataSet();
            this.timeline = new Timeline(this.$refs.timeline, this.events, this.options);
            this.getEvents();
            this.getTags();
            this.timeline.on('select', function (selected) {
                // If the selection is empty, we don't want to do anything.
                if (selected.items.length > 0) {
                    // Equivalent to `this.$dispatch('timeline-select', selected.items[0]);`
                    // but `this.$dispatch` is not accessible here.
                    window.dispatchEvent(new CustomEvent('timeline-select', { detail: selected.items[0] }));
                }
            });
            this.timeline.on('contextmenu', function (props) {
                props.event.preventDefault();
                window.dispatchEvent(new CustomEvent('add-event', { detail: props }));
            });
        },
        getEvents() {
            axios.get('/events')
                .then(response => {
                    response.data.forEach(e => {
                        this.events.add({
                            id: e.id,
                            content: e.name,
                            title: new Date(e.date).toLocaleDateString(),
                            start: e.date
                        });
                    });
                    this.timeline.fit();
                }).catch(() => {
                    this.$dispatch('notify', {type: 'error', content: 'Impossible de charger les événements.'});
                });
        },
        getTags() {
            axios.get('/tags')
                .then(response => {
                    this.tags = response.data;
                }).catch(() => {
                    this.$dispatch('notify', {type: 'error', content: 'Impossible de charger les tags.'});
                });
        },
        openFlyout: false,
        currentEvent: {
            id: null,
            name: null,
            description: null,
            date: null,
            tags: []
        },
        mode: 'add',
        show(event) {
            this.mode = 'show';
            this.openFlyout = true;
            if (this.currentEvent.id !== event.detail) {
                this.currentEvent = { id: null, name: null, description: null, date: null, tags: [] };
                axios.get('/events/' + event.detail).then(response => {
                    this.currentEvent = response.data;
                }).catch(() => {
                    this.$dispatch('notify', {type: 'error', content: `Impossible de charger l'événements.`});
                })
            }
        },
        preventDelete: true,
        requestInProgress: false,
        deleteEvent() {
            if (this.preventDelete) {
                this.preventDelete = false;
                setTimeout(() => {
                    this.preventDelete = true;
                }, 3000)
            } else {
                this.preventDelete = true;
                this.requestInProgress = true;
                axios.delete('/events/' + this.currentEvent.id)
                    .then(() => {
                        this.events.remove(this.currentEvent.id);
                        this.openFlyout = false;
                        this.currentEvent = { id: null, name: null, description: null, date: null, tags: [] };
                        this.$dispatch('notify', { content: `L'événement a bien été supprimé.`, type: 'success' })
                    }).catch((error) => {
                        if (error.status === 404) {
                            this.$dispatch('notify', { content: `L'événement est introuvable.`, type: 'error' })
                        } else {
                            this.$dispatch('notify', { content: `Une erreur s'est produite lors de la suppression.`, type: 'error' })
                        }
                    }).finally(() => { this.requestInProgress = false; })
            }
        },
        formErrors: { name: [], description: [], date: [], tags: [] },
        updateEvent() {
            this.requestInProgress = true;
            this.formErrors = { name: [], description: [], date: [], tags: [] };
            axios.put('/events/' + this.currentEvent.id, this.currentEvent)
            .then(response => {
                this.$dispatch('notify', { content: `${response.data.name} a bien été modifié.`, type: 'success' })
                this.events.update([{
                    id: response.data.id,
                    content: response.data.name,
                    title: new Date(response.data.date).toLocaleDateString(),
                    start: response.data.date
                }]);
                this.currentEvent = response.data;
                this.mode = 'show';
            }).catch(error => {
                if (error.response.status === 422) {
                    this.formErrors = error.response.data.errors
                } else {
                    this.$dispatch('notify', { content: `Une erreur s'est produite lors de la modification.`, type: 'error' })
                }
            }).finally(() => { this.requestInProgress = false; })
        },
        showAddForm(e) {
            this.mode = 'add';
            this.openFlyout = true;
            if (e.detail.snappedTime) {
                date = e.detail.snappedTime.format('YYYY-MM-DD HH:mm');
                this.currentEvent = { id: null, name: null, description: null, date: date, tags: [] };
            } else {
                this.currentEvent = { id: null, name: null, description: null, date: null, tags: [] };
            }
            this.formErrors = { name: [], description: [], date: [], tags: [] };
        },
        addEvent() {
            this.requestInProgress = true;
            this.formErrors = { name: [], description: [], date: [], tags: [] };
            axios.post('/events', {
                name: this.currentEvent.name,
                description: this.currentEvent.description,
                date: this.currentEvent.date,
                tags: this.currentEvent.tags
            }).then(response => {
                this.events.add({
                    id: response.data.id,
                    content: response.data.name,
                    title: new Date(response.data.date).toLocaleDateString(),
                    start: response.data.date
                });
                this.$dispatch('notify', { content: `${response.data.name} a bien été créé.`, type: 'success' })
                this.openFlyout = false;
                this.currentEvent = { id: null, name: null, description: null, date: null, tags: [] }
            }).catch(error => {
                if (error.response.status === 422) {
                    this.formErrors = error.response.data.errors
                } else {
                    this.$dispatch('notify', { content: `Une erreur s'est produite lors de l'ajout.`, type: 'error' })
                }
            }).finally(() => { this.requestInProgress = false; })
        }
    }"
    @timeline-select.window="show($event)"
    @add-event.window="showAddForm($event)"
    class="w-full h-full flex items-center bg-white"
>
    <!-- Loading overlay -->
    <x-loading-overlay x-show="loading"/>

    <!-- Event flyout -->
    <x-flyout x-model="openFlyout">
        <x-events.show x-show="mode === 'show'"/>
        <x-events.form x-show="mode === 'add' || mode === 'edit'"/>
    </x-flyout>

    <!-- Add event button -->
    <div class="fixed bottom-0 right-0 pr-8 pb-8 z-10 md:pr-12 md:pb-12">
        <button
            @click="$dispatch('add-event')"
            type="button"
            class="flex items-center justify-center space-x-1 whitespace-nowrap rounded-full bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow hover:shadow-xl hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 transition"
        >
            <x-icons.plus size="size-6"/>
            <span class="text-lg">Événement</span>
        </button>
    </div>

    <div x-cloak x-ref='timeline' class="w-full relative"></div>
</div>
