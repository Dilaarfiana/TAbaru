<!-- resources/views/components/stat-card.blade.php -->
@props(['icon', 'value', 'label', 'color', 'percentage', 'trend'])

@php
$colorClasses = [
    'blue' => [
        'bg' => 'bg-blue-100',
        'text' => 'text-blue-500',
        'badge' => 'bg-blue-100 text-blue-700'
    ],
    'red' => [
        'bg' => 'bg-red-100',
        'text' => 'text-red-500',
        'badge' => 'bg-red-100 text-red-700'
    ],
    'yellow' => [
        'bg' => 'bg-yellow-100',
        'text' => 'text-yellow-500',
        'badge' => 'bg-yellow-100 text-yellow-700'
    ],
    'green' => [
        'bg' => 'bg-green-100',
        'text' => 'text-green-500',
        'badge' => 'bg-green-100 text-green-700'
    ]
];
@endphp

<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-4">
        <div class="w-10 h-10 rounded-lg {{ $colorClasses[$color]['bg'] }} flex items-center justify-center {{ $colorClasses[$color]['text'] }}">
            <i class="fas {{ $icon }}"></i>
        </div>
        <span class="text-xs font-medium {{ $colorClasses[$color]['badge'] }} px-2 py-1 rounded">
            {{ $percentage }}% 
            <i class="fas fa-arrow-{{ $trend }}"></i>
        </span>
    </div>
    <h3 class="text-2xl font-bold mb-1">{{ $value }}</h3>
    <p class="text-gray-500 text-sm">{{ $label }}</p>
</div>