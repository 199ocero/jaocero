{{-- Maps a social network name (as returned by About::socialLinks()) to its icon. --}}
@props(['network'])

@switch($network)
    @case('GitHub') <x-icons.github {{ $attributes }} /> @break
    @case('LinkedIn') <x-icons.linkedin {{ $attributes }} /> @break
    @case('X') <x-icons.x {{ $attributes }} /> @break
    @default <x-icons.mail {{ $attributes }} />
@endswitch
