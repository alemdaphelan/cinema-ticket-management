@props([
    'action' => '#',
    'method' => 'POST',
])

<form
    action="{{ $action }}"
    method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"
    {{ $attributes->merge([
        'class' => 'space-y-4'
    ]) }}
>
    @csrf

    @if (!in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endif

    {{ $slot }}
</form>