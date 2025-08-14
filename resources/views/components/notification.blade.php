<div x-data="{
        notifications: [],
        add(e) {
            this.notifications.push({
                id: e.timeStamp,
                type: e.detail.type,
                content: e.detail.content
            })
        },
        remove(notification) {
            this.notifications = this.notifications.filter(i => i.id !== notification.id)
        }
    }"
     @notify.window="add($event)"
     class="fixed bottom-0 left-0 flex w-full max-w-sm flex-col space-y-4 pl-1.5 pb-1.5 z-50 sm:justify-start md:pl-4 md:pb-4"
     role="status"
     aria-live="polite"
>
    <!-- Notification -->
    <template x-for="notification in notifications" :key="notification.id">
        <div
            x-data="{
                show: false,
                typeDisplay: {
                    info: 'Information',
                    success: 'Effectué',
                    error: 'Erreur'
                },
                init() {
                    this.$nextTick(() => this.show = true);
                    setTimeout(() => this.transitionOut(), 3000);
                },
                transitionOut() {
                    this.show = false;
                    setTimeout(() => this.remove(this.notification), 500);
                }
            }"
            x-show="show"
            x-transition.duration.500ms
            class="pointer-events-auto relative w-full max-w-sm rounded-lg border border-gray-200 bg-white p-2 shadow-lg"
        >
            <div class="flex items-start gap-4">
                <div class="flex-1 py-1.5 pl-2.5 flex gap-2">
                    <!-- Icons -->
                    <div x-show="notification.type === 'info'" class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="mt-0.5 size-4 text-gray-600">
                            <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0ZM9 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6.75 8a.75.75 0 0 0 0 1.5h.75v1.75a.75.75 0 0 0 1.5 0v-2.5A.75.75 0 0 0 8.25 8h-1.5Z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Information :</span>
                    </div>

                    <div x-show="notification.type === 'success'" class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="mt-0.5 size-4 text-green-600">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.15-.043l4.25-5.5Z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Réussite :</span>
                    </div>

                    <div x-show="notification.type === 'error'" class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="mt-0.5 size-4 text-red-600">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Erreur :</span>
                    </div>

                    <!-- Text -->
                    <div class="flex flex-col gap-y-2">
                        <p x-text="typeDisplay[notification.type]" class="capitalize font-medium text-sm text-gray-800"></p>

                        <div class="text-sm text-gray-600" x-text="notification.content"></div>
                    </div>
                </div>
                <!-- Remove button -->
                <div class="flex items-center">
                    <button @click="transitionOut()" type="button" class="inline-flex items-center font-medium justify-center p-1.5 rounded-md hover:bg-gray-800/5 text-gray-400 hover:text-gray-800">
                        <x-icons.cross size="size-5"/>
                        <span class="sr-only">Fermer la notification</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
