<div class="w-full overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
    <table {{ $attributes->merge([
        'class' => 'w-full text-left text-sm text-gray-700'
    ]) }}>
        {{ $slot }}
    </table>
</div>