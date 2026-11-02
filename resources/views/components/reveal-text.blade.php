{{--
    Reveals text word by word. Phrases wrapped in *asterisks* get the marker highlight,
    which draws in right after its words appear.
--}}
@props(['text', 'delay' => 0, 'step' => 70])

@php($wordIndex = 0)
@foreach (\App\Support\Highlight::segments($text) as $segment)
    @if ($segment['highlighted'])
        <span class="marker marker-animated" style="--marker-delay: {{ $delay + ($wordIndex + count(preg_split('/\s+/', $segment['text']))) * $step }}ms">
    @endif
    @foreach (preg_split('/\s+/', $segment['text']) as $word)
        <span class="inline-block motion-safe:animate-word" style="animation-delay: {{ $delay + $wordIndex++ * $step }}ms">{{ $word }}</span>
    @endforeach
    @if ($segment['highlighted'])
        </span>
    @endif
@endforeach
