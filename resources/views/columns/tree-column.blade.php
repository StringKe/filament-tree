@php
    $state = $getState();
    $depth = $getDepth($getRecord());
    $hasChildren = $hasChildren($getRecord());
    $isExpanded = $isExpanded($getRecord());
    $showIndent = $getShowIndent();
    $indentSize = $getIndentSize();
    $showIcon = $getShowIcon();
@endphp

<div 
    x-data="{ expanded: @js($isExpanded) }"
    class="filament-tree-column flex items-center"
    data-tree-id="{{ $getRecord()->getKey() }}"
    data-tree-parent="{{ $getRecord()->getAttribute('parent_id') }}"
    data-tree-depth="{{ $depth }}"
>
    @if ($showIndent && $depth > 0)
        <span style="width: {{ $depth * $indentSize }}px;" class="inline-block"></span>
    @endif

    @if ($showIcon)
        @if ($hasChildren)
            <button
                type="button"
                x-on:click="expanded = !expanded; $dispatch('tree-toggle', { id: '{{ $getRecord()->getKey() }}' })"
                class="filament-tree-toggle-button p-0.5 -m-0.5 hover:bg-gray-50 dark:hover:bg-gray-800 rounded"
            >
                <x-filament::icon
                    :icon="$expanded ? $getCollapseIcon() : $getExpandIcon()"
                    class="h-4 w-4 text-gray-500 dark:text-gray-400 transition-transform"
                    :class="{ 'rotate-90': !expanded }"
                />
            </button>
        @else
            <span class="inline-block p-0.5 -m-0.5">
                <x-filament::icon
                    :icon="$getLeafIcon()"
                    class="h-4 w-4 text-gray-300 dark:text-gray-600"
                />
            </span>
        @endif
    @endif

    <span class="ml-2">
        {{ $state }}
    </span>
</div>