<span {{ $attributes }}
      class="cursor-default inline-flex items-center gap-x-1.5 rounded-full px-2 py-1 text-xs font-medium text-gray-900 ring-1 ring-gray-200 ring-inset"
>
  <svg class="size-1.5" fill="currenColor" viewBox="0 0 6 6" aria-hidden="true">
    <circle cx="3" cy="3" r="3"/>
  </svg>
  {{ $slot }}
</span>
