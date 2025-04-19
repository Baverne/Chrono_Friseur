<div {{ $attributes->merge(['class' => "flex items-center justify-between"]) }}>
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
