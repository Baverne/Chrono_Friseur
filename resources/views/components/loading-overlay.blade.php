<div {{ $attributes->merge(['class' => 'fixed inset-0 bg-indigo-600 z-50']) }}>
    <div class="w-full h-full flex justify-center items-center">
        <p class="text-white animate-pulse font-bold inline-flex items-center space-x-2">
            <x-icons.spinner size="size-8"/>

            <span class="uppercase text-2xl">Chargement...</span>
        </p>
    </div>
</div>
