<div {{ $attributes->merge(['class' => 'flex flex-col my-2']) }}
     x-data="{
        formTag: {
            id: null,
            name: { fr: null },
            color: '#000000'
        },
        tagFormErrors: { name: [], color: [] },
        showAddTag() {
            this.mode = 'addTag';
            this.formTag = { id: 0, name: { fr: '' }, color: '#000000' };
            this.tagFormErrors = { name: [], color: [] };
        }
    }"
>
    <ul role="list" class="divide-y text-gray-800 divide-gray-300 -mb-4">
        <template x-for="tag of tags.get({fields: ['id', 'color', 'name']})" :key="tag.id">
        <li class="w-full py-5 px-2 hover:bg-gray-50">
            <x-tags.show x-show="mode === 'listTags' || formTag?.id !== tag.id"/>
            <x-tags.form x-show="mode === 'editTag' && formTag?.id === tag.id"/>
        </li>
        </template>
    </ul>

    <div class="relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center">
            <button @click="showAddTag()" type="button"
                    class="inline-flex items-center gap-x-1.5 rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50 transition"
            >
                <x-icons.plus class="-mr-0.5 -ml-1 text-gray-400" size="size-5"/>

                <span class="sr-only">Ajouter un tag</span> <span aria-hidden="true">Ajouter</span>
            </button>
        </div>
    </div>

    <div x-show="mode === 'addTag'" class="w-full py-5 px-2 -mt-4 hover:bg-gray-50">
        <x-tags.form/>
    </div>
</div>
