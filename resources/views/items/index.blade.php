<x-layout>
    <x-slot name="header">
        Inventory Page
    </x-slot>

    <div class="space-y-4">
        @foreach ($items as $item)
            <a href="/items/{{ $item['id'] }}"
                class="block px-4 py-6 mb-4 bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg hover:bg-gray-50 transition duration-200 ease-in-out">
                <div class="font-bold text-blue-500 text-sm">{{ $item->supplier->name }}</div>
                <div class="mt-2">
                    <strong class="text-gray-900">{{ $item['name'] }}:</strong>
                    <span class="text-gray-600">Price ₱ {{ number_format($item['price'], 2) }} per piece.</span>
                </div>
            </a>
        @endforeach

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</x-layout>