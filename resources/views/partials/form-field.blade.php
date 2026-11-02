@php
    $type ??= 'text';
    $optional ??= false;
    $textarea ??= false;
    $autocomplete ??= 'off';
    $inputClasses = 'w-full rounded-xl border border-line bg-elevated px-4 py-3 text-fg placeholder:text-subtle focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30';
@endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-sm font-medium">
        {{ $label }}
        @if ($optional)
            <span class="font-normal text-subtle">(optional)</span>
        @endif
    </label>

    @if ($textarea)
        <textarea id="{{ $name }}" wire:model="{{ $name }}" rows="6" @class([$inputClasses, 'border-red-500' => $errors->has($name)]) @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror></textarea>
    @else
        <input id="{{ $name }}" type="{{ $type }}" wire:model="{{ $name }}" autocomplete="{{ $autocomplete }}" @class([$inputClasses, 'border-red-500' => $errors->has($name)]) @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>
