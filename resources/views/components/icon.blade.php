@props(['name', 'class' => 'w-4 h-4'])

@php
    $stroke = 'stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"';
@endphp

@switch($name)
    @case('plus')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <path d="M5 12h14" /><path d="M12 5v14" />
        </svg>
        @break

    @case('list')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <line x1="8" x2="21" y1="6" y2="6" /><line x1="8" x2="21" y1="12" y2="12" /><line x1="8" x2="21" y1="18" y2="18" />
            <line x1="3" x2="3.01" y1="6" y2="6" /><line x1="3" x2="3.01" y1="12" y2="12" /><line x1="3" x2="3.01" y1="18" y2="18" />
        </svg>
        @break

    @case('refresh')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
            <path d="M3 3v5h5" />
            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
            <path d="M16 16h5v5" />
        </svg>
        @break

    @case('search')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" />
        </svg>
        @break

    @case('pencil')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
            <path d="m15 5 4 4" />
        </svg>
        @break

    @case('trash')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <path d="M3 6h18" />
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
            <line x1="10" x2="10" y1="11" y2="17" /><line x1="14" x2="14" y1="11" y2="17" />
        </svg>
        @break

    @case('chevron-down')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <path d="m6 9 6 6 6-6" />
        </svg>
        @break

    @case('image')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
            <circle cx="9" cy="9" r="2" />
            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
        </svg>
        @break

    @case('lock')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
        @break

    @case('x')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $stroke !!} class="{{ $class }}">
            <path d="M18 6 6 18" /><path d="m6 6 12 12" />
        </svg>
        @break

    @case('bxs:sort-alt')
    <svg xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24"
         class="{{ $class }}"
         fill="currentColor">
        <path d="M6.227 11h11.547c.862 0 1.32-1.02.747-1.665L12.748 2.84a.998.998 0 0 0-1.494 0L5.479 9.335C4.906 9.98 5.364 11 6.227 11zm5.026 10.159a.998.998 0 0 0 1.494 0l5.773-6.495c.574-.644.116-1.664-.747-1.664H6.227c-.862 0-1.32 1.02-.747 1.665z"/>
    </svg>
    @break
 
    @default
        {{-- unknown icon name: render nothing rather than break the layout --}}
@endswitch
