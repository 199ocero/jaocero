{{--
    The AI assistant's avatar: a <robot-head> (resources/js/robot-head.js) in the shape and colors from Site settings.
    Bind its face to the chat with x-bind:state. Small heads drop the antenna and floor shadow.
--}}
@props(['size' => 36, 'state' => null, 'paused' => false, 'interactive' => true])

@php
    $avatar = config('portfolio.chat.avatar');
    $isSmall = $size < 64;
@endphp

<robot-head
    wire:ignore
    size="{{ $size }}"
    shape="{{ $avatar['shape'] }}"
    color="{{ $avatar['color'] }}"
    trim-color="{{ $avatar['trim_color'] }}"
    screen-color="{{ $avatar['screen_color'] }}"
    @if ($state) state="{{ $state }}" @endif
    @if ($isSmall) antenna="false" floor-shadow="false" @endif
    @if ($paused) paused="true" @endif
    @unless ($interactive) interactive="false" @endunless
    {{ $attributes->merge(['style' => "width: {$size}px; height: {$size}px"])->class('block shrink-0') }}
></robot-head>
