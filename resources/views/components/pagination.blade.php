@props(['paginator'])

@php
    $lastPage = max($paginator->lastPage(), 1);
@endphp

<div class="flex items-center gap-1">
    <a href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}"
       @if ($paginator->onFirstPage()) aria-disabled="true" @endif
       class="px-3 py-1.5 rounded border text-sm {{ $paginator->onFirstPage() ? 'text-gray-300 border-gray-200 cursor-not-allowed' : 'text-gray-700 border-gray-300 hover:bg-gray-50' }}">
        Previous
    </a>

    @for ($i = 1; $i <= $lastPage; $i++)
        <a href="{{ $paginator->url($i) }}"
           class="px-3 py-1.5 rounded text-sm min-w-[36px] text-center {{ $paginator->currentPage() == $i ? 'bg-[#0f1f3d] text-white font-semibold' : 'border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            {{ $i }}
        </a>
    @endfor

    <a href="{{ $paginator->hasMorePages() ? $paginator->nextPageUrl() : '#' }}"
       @if (!$paginator->hasMorePages()) aria-disabled="true" @endif
       class="px-3 py-1.5 rounded border text-sm {{ $paginator->hasMorePages() ? 'text-gray-700 border-gray-300 hover:bg-gray-50' : 'text-gray-300 border-gray-200 cursor-not-allowed' }}">
        Next
    </a>
</div>