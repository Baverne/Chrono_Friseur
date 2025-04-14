<div {{ $attributes->merge(['class' => 'flex flex-col my-2 space-y-4 border-b border-gray-900/10 pb-6']) }}>
    <p x-text="currentEvent.name" class="font-semibold text-lg"></p>

    <template x-if="currentEvent.tags.length > 0">
    <div class="space-x-1">
        <template x-for="tag in currentEvent.tags" :key="tag.id">
        <x-tags.badge x-bind:style="`fill: ${tags.get(tag.id).color ?? 'black'}`">
            <span x-text="tags.get(tag.id).name.fr"></span>
        </x-tags.badge>
        </template>
    </div>
    </template>

    <p x-cloak x-text="moment(currentEvent.date).format('llll') + ' (' + moment(currentEvent.date).fromNow() + ')'"></p>

    <p x-text="currentEvent.description" class="text-gray-500"></p>
</div>
