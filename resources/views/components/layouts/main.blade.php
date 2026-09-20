@props(['title' => 'CineStar', 'header' => ''])

@if(Auth::check() && Auth::user()->role === 'staff')
    <x-layouts.staff :title="$title" :header="$header">
        {{ $slot }}
    </x-layouts.staff>
@else
    <x-layouts.app :title="$title">
        {{ $slot }}
    </x-layouts.app>
@endif
