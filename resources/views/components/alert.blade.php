@props(['type' => 'info', 'dismissible' => true])

@php
$classes = [
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
];

$icons = [
    'info' => 'fas fa-info-circle',
    'success' => 'fas fa-check-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'error' => 'fas fa-times-circle',
];
@endphp

<div {{ $attributes->merge(['class' => 'border-l-4 p-4 mb-4 ' . $classes[$type]]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="{{ $icons[$type] }}"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">
                {{ $slot }}
            </p>
        </div>
        @if($dismissible)
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button type="button" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                        class="inline-flex rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-gray-600">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
