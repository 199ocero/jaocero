{{-- Live preview of the chat avatar in Site settings; follows the shape and color fields as they change. --}}
<div x-data class="flex h-full min-h-48 flex-col items-center justify-center gap-4 rounded-xl bg-gray-50 p-6 dark:bg-white/5">
    <robot-head
        wire:ignore
        size="112"
        x-bind:shape="$wire.data.robot_shape"
        x-bind:color="$wire.data.robot_color"
        x-bind:trim-color="$wire.data.robot_trim_color"
        x-bind:screen-color="$wire.data.robot_screen_color"
    ></robot-head>
    <p class="text-xs text-gray-500 dark:text-gray-400">Preview · click it</p>
</div>
