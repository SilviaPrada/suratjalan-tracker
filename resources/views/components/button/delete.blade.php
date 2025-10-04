<button {{ $attributes->merge([
    'class' => 'flex items-center gap-1 px-3 py-1 border border-red-500 text-red-500 rounded
                hover:bg-red-500 hover:text-white
                dark:border-red-400 dark:text-red-400
                dark:hover:bg-red-400 dark:hover:text-white
                transition-colors duration-200'
]) }}>
    <x-heroicon-o-trash class="h-4 w-4" />
    <span>{{ $slot }}</span>
</button>
