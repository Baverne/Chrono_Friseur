<form
    {{ $attributes }}
    x-data="{
        submit() {
            if (mode === 'editTag') {
                this.updateTag();
            } else if (mode === 'addTag') {
                this.addTag();
            }
        },
        updateTag() {
            this.tagRequestInProgress[this.formTag.id] = true;
            this.tagFormErrors = { name: [], color: [] };
            axios.put('/tags/' + this.formTag.id, this.formTag)
                .then(response => {
                    this.tags.updateOnly(this.makeTagData(response.data));
                    this.mode = 'listTags';
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.tagFormErrors = error.response.data.errors
                    } else {
                        this.$dispatch('notify', { content: `Une erreur s'est produite lors de la modification.`, type: 'error' })
                    }
                }).finally(() => { this.tagRequestInProgress[this.formTag.id] = false; })
        },
        addTag() {
            this.tagRequestInProgress[this.formTag.id] = true;
            this.tagFormErrors = { name: [], color: [] };
            axios.post('/tags', this.formTag)
                .then(response => {
                    this.tags.add(this.makeTagData(response.data));
                    this.tagRequestInProgress[response.data.id] = false;
                    this.preventTagDelete[response.data.id] = true;
                    this.mode = 'listTags';
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.tagFormErrors = error.response.data.errors;
                    } else {
                        this.$dispatch('notify', { content: `Une erreur s'est produite lors de l'ajout.`, type: 'error' });
                    }
                }).finally(() => { this.tagRequestInProgress[this.formTag.id] = false; })
        }
    }"
    @submit.prevent="submit()"
    :id="`form-tag-${formTag.id ?? 'add'}`"
    class="flex flex-col w-full"
>
    <div class="flex items-center justify-between w-full text-gray-800">
        <div class="flex space-x-2 items-center justify-start">
            <!-- Color -->
            <div class="flex">
                <label for="color" class="sr-only">Couleur</label>

                <input x-model="formTag.color" type="color" name="color" id="color"
                       class="block w-5 h-8 bg-transparent outline-0 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                >
            </div>

            <!-- Name -->
            <div class="flex">
                <label for="name" class="sr-only">Nom</label>

                <input x-model="formTag.name.fr" type="text" name="name" id="name" placeholder="Nom"
                       class="block w-full rounded-md bg-white px-3 py-1 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                >
            </div>
        </div>

        <div class="flex space-x-1 items-center justify-end">
            <button x-tooltip="'Annuler'" @click="mode = 'listTags'" type="button"
                    class="relative flex items-center justify-center whitespace-nowrap rounded-full p-1.5 text-gray-800 font-semibold outline-0 outline-transparent  hover:bg-gray-800/10 focus:outline-2 focus:outline-gray-600 transition"
            >
                <x-icons.cross size="size-5"/>
            </button>

            <button x-tooltip="'Appliquer'" type="submit" :disabled="tagRequestInProgress[formTag.id]"
                    class="relative flex items-center justify-center whitespace-nowrap rounded-full p-1.5 text-indigo-500 font-semibold outline-0 outline-transparent hover:text-white hover:bg-indigo-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 transition"
                    :class="{'opacity-50 cursor-not-allowed': tagRequestInProgress[formTag.id]}"
            >
                <template x-if="!tagRequestInProgress[formTag.id]">
                <x-icons.check size="size-5"/>
                </template>

                <template x-if="tagRequestInProgress[formTag.id]">
                <x-icons.spinner size="size-5"/>
                </template>
            </button>
        </div>
    </div>

    <template x-for="error in tagFormErrors.color" :key="error">
    <x-form-error x-text="error"/>
    </template>

    <template x-for="error in tagFormErrors['name.fr']" :key="error">
    <x-form-error x-text="error"/>
    </template>
</form>
