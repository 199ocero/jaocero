{{--
    The page backdrop: a soft sunset in the light theme and a starry night in the dark one.
    Purely decorative. Styles and theme-switch animations live in resources/css/app.css (".sky").
--}}
@php
    // A fixed seed keeps the constellation identical on every page load.
    $randomizer = new \Random\Randomizer(new \Random\Engine\Mt19937(1337));

    $stars = collect(range(1, 90))->map(function () use ($randomizer): array {
        $isBright = $randomizer->nextFloat() < 0.1;

        return [
            'x' => round($randomizer->nextFloat() * 100, 2),
            // Bias stars toward the top of the sky.
            'y' => round(($randomizer->nextFloat() ** 1.6) * 92, 2),
            'size' => $isBright ? round(2.2 + $randomizer->nextFloat(), 2) : round(1 + $randomizer->nextFloat() * 1.1, 2),
            'twinkleDuration' => round(2.5 + $randomizer->nextFloat() * 4, 2),
            'twinkleDelay' => round($randomizer->nextFloat() * -6, 2),
            'appearDelay' => round(0.3 + $randomizer->nextFloat() * 1.6, 2),
        ];
    });
@endphp

<div {{ $attributes->class('sky') }} aria-hidden="true">
    <div class="sky-day">
        <div class="sky-sunset"></div>
        <div class="sky-sun"></div>
        <div class="sky-cloud" style="--cloud-top: 62%; --cloud-left: 8%; --cloud-width: 420px; --cloud-duration: 70s"></div>
        <div class="sky-cloud" style="--cloud-top: 74%; --cloud-left: 52%; --cloud-width: 520px; --cloud-duration: 95s"></div>
        <div class="sky-cloud" style="--cloud-top: 30%; --cloud-left: 70%; --cloud-width: 300px; --cloud-duration: 80s"></div>
    </div>

    <div class="sky-night">
        <div class="sky-moon"></div>

        @foreach ($stars as $star)
            <span
                class="sky-star"
                style="--x: {{ $star['x'] }}%; --y: {{ $star['y'] }}%; --size: {{ $star['size'] }}px; --twinkle-duration: {{ $star['twinkleDuration'] }}s; --twinkle-delay: {{ $star['twinkleDelay'] }}s; --appear-delay: {{ $star['appearDelay'] }}s"
            ></span>
        @endforeach

        <span class="sky-shooting-star" style="--x: 78%; --y: 8%; --cycle: 11s; --delay: 1.1s"></span>
        <span class="sky-shooting-star" style="--x: 42%; --y: 4%; --cycle: 17s; --delay: 6.5s"></span>
    </div>
</div>
