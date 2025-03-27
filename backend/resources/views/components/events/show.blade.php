<div {{ $attributes->merge(['class' => 'flex flex-col mt-2 border-b border-gray-900/10 pb-6']) }}>
    <div class="row mb-2">
        <p x-text="currentEvent.name" class="font-semibold text-lg"></p>
    </div>

    <div class="row mb-2">
        <p x-text="currentEvent.date"></p>
    </div>

    <div class="row mb-2">
        <p x-text="currentEvent.description" class="text-gray-500"></p>
    </div>
</div>
