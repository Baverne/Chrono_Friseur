<div
    x-data="{
        loading: true,
        events: new DataSet(),
        tags: new DataSet(),
        timeline: null,
        options: {
            start: '1020-01-01',
            end: '2030-12-31',
            onInitialDrawComplete: null,
            margin: {
                item: 20
            },
            height: '100%',
            orientation: 'top',
            locale: 'fr',
            xss: {
                filterOptions: {
                    whiteList: {
                        h1: ['class', 'x-tooltip'],
                        ul: ['class'],
                        template: ['x-for', ':key'],
                        li: ['class', 'x-data'],
                        span: ['class', ':style', 'x-tooltip']
                    }
                }
            },
            template: function (item) {
                let html = `<h1 x-tooltip=&quot;'${moment(item.start).format('LL')}'&quot;>${item.content}</h1>`;
                if (item.tags.length > 0) {
                    html += `<ul class='absolute -top-1 left-0.5 flex space-x-1 items-start h-2 font-extrabold'>`;
                    html += `<template x-for='tag in events.get(${item.id})?.tags' :key='tag?.id'>`;
                    html += `<li class='inline-flex size-2'>
                                 <span x-tooltip='tags.get(tag.id)?.name.fr'
                                       :style='\`background-color: \${tags.get(tag.id)?.color}\`'
                                       class='size-2 rounded-full shadow-sm hover:scale-150 transition'
                                 ></span>
                             </li>`;
                    html += '</template></ul>';
                }
                return html;
            }
        },
        init() {
            this.options.onInitialDrawComplete = () => { this.loading = false };
            this.timeline = new Timeline(this.$refs.timeline, this.events, this.options);
            this.getData();
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
        makeTagData(tag) {
            return {
                id: tag.id,
                color: tag.color,
                name: { fr: tag.name.fr }
            }
        },
        tagRequestInProgress: { 0: false },
        preventTagDelete: { 0: true },
        getData() {
            // Get tags, then get events
            this.tags.clear();
            axios.get('/tags')
                .then(response => {
                    response.data.forEach(t => {
                        this.tags.add(this.makeTagData(t));
                        this.tagRequestInProgress[t.id] = false;
                        this.preventTagDelete[t.id] = true;
                    })
                    this.getEvents();
                }).catch(() => {
                    this.$dispatch('notify', {type: 'error', content: 'Impossible de charger les tags.'});
                });
        },
        makeEventData(event) {
            return {
                id: event.id,
                content: event.name,
                start: event.date,
                name: event.name,
                description: event.description,
                date: event.date,
                tags: event.tags.map(t => this.tags.get(t.id))
            };
        },
        eventRequestInProgress: false,
        getEvents(filters = null) {
            axios.get('/events', { params: filters })
                .then(response => {
                    this.$dispatch('events-loaded');
                    this.events.clear();
                    response.data.forEach(e => {
                        this.events.add(this.makeEventData(e));
                    });
                    this.timeline.fit();
                }).catch(error => {
                    if (error.status === 422) {
                        this.$dispatch('events-errored', {errors: error.response.data.errors})
                    } else {
                        this.$dispatch('notify', {type: 'error', content: 'Impossible de charger les événements.'});
                    }
                }).finally(() => { this.eventRequestInProgress = false; });
        },
        mode: '',
        openEventFlyout: false,
        formEvent: {
            id: null,
            name: null,
            description: null,
            date: null,
            tags: []
        },
        eventFormErrors: { name: [], description: [], date: [], tags: [] },
        showAddForm(e) {
            this.mode = 'addEvent';
            this.openEventFlyout = true;
            if (e.detail.snappedTime) {
                date = e.detail.snappedTime.format('YYYY-MM-DD HH:mm');
                this.formEvent = { id: null, name: null, description: null, date: date, tags: [] };
            } else {
                this.formEvent = { id: null, name: null, description: null, date: null, tags: [] };
            }
            this.eventFormErrors = { name: [], description: [], date: [], tags: [] };
        },
        selectedEvent: {
            id: null,
            name: null,
            description: null,
            date: null,
            tags: []
        },
        showEvent(event) {
            this.mode = 'showEvent';
            this.openEventFlyout = true;
            if (this.selectedEvent.id !== event.detail) {
                var e = this.events.get(event.detail);
                this.selectedEvent = {
                    id: e.id,
                    name: e.name,
                    description: e.description,
                    date: e.date,
                    tags: e.tags
                };
            }
        },
        openTagFlyout: false,
        showListTags() {
            this.mode = 'listTags';
            this.openTagFlyout = true;
        },
        openSearchFlyout: false,
        showSearch() {
            this.openSearchFlyout = true;
            this.mode = 'search';
        }
    }"
    @timeline-select.window="showEvent($event)"
    @add-event.window="showAddForm($event)"
    @list-tags.window="showListTags()"
    @open-search.window="showSearch()"
    class="w-full h-full flex items-center bg-white rounded-lg"
>
    <!-- Loading overlay -->
    <x-loading-overlay x-show="loading"/>

    <div x-cloak x-ref='timeline' class="relative w-full h-full rounded-lg"></div>

    <!-- Event flyout -->
    <x-flyout x-model="openEventFlyout">
        <!-- Title -->
        <x-slot:title>
            <span x-show="mode === 'showEvent'">Détails de l'événement</span>
            <span x-show="mode === 'editEvent'">Modifier l'événement</span>
            <span x-show="mode === 'addEvent'">Ajouter un événement</span>
        </x-slot:title>

        <!-- Animation when loading event data -->
        <template x-if="(!selectedEvent.id && mode === 'showEvent') || (!formEvent.id && mode === 'editEvent')">
        <div class="space-y-3 animate-pulse">
            <div class="rounded-md bg-gray-200/70 h-5 w-[300px]"></div>
            <div class="rounded-md bg-gray-200/70 h-5 w-[250px]"></div>
            <div class="rounded-md bg-gray-200/70 h-5 w-[200px]"></div>
        </div>
        </template>

        <!-- showEvent -->
        <x-events.show x-show="selectedEvent.id && mode === 'showEvent'"/>

        <!-- editEvent || addEvent -->
        <x-events.form x-show="(formEvent.id && mode === 'editEvent') || mode === 'addEvent'"/>
    </x-flyout>

    <!-- Tag flyout -->
    <x-flyout x-model="openTagFlyout">
        <!-- Title -->
        <x-slot:title>
            <span x-show="mode === 'listTags' || mode === 'editTag' || mode === 'addTag'">Gestion des tags</span>
        </x-slot>

        <x-tags.list/>
    </x-flyout>

    <!-- Search flyout -->
    <x-flyout x-model="openSearchFlyout">
        <!-- Title -->
        <x-slot:title>
            <span x-show="mode === 'search'">Rechercher & Filtrer par&mldr;</span>
        </x-slot>

        <x-timeline.search/>
    </x-flyout>

    <!-- Bottom elements -->
    <div class="fixed bottom-0 left-0 pb-8 px-4 w-full z-10 md:pb-7">
        <x-timeline.bottom-elements/>
    </div>
</div>
