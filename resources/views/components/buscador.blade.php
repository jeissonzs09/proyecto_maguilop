<div x-data="{ search: '{{ request($param) }}' }" class="mb-4">
    <input
        type="text"
        x-model="search"
        @input.debounce.500="window.location.href = '?{{ $param }}=' + encodeURIComponent(search)"
        placeholder="{{ $placeholder }}"
        class="w-full px-4 py-2 border rounded shadow-sm focus:ring focus:ring-indigo-200"
    >
</div>
