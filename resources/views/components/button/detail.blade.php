<a {{ $attributes->merge([
    'class' => 'flex items-center gap-1 px-3 py-1 border border-blue-500 text-blue-500 rounded
                hover:bg-blue-500 hover:text-white
                dark:border-blue-400 dark:text-blue-400
                dark:hover:bg-blue-400 dark:hover:text-white
                transition-colors duration-200'
]) }}>
    <x-heroicon-o-pencil-square class="h-4 w-4" />
    <span>{{ $slot }}</span>
</a>
