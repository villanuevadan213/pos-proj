<x-layout>
    <x-slot name="header">
        Sales Page
    </x-slot>

    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($sales as $transaction)
                            @php
                                $method = strtolower($transaction->payment_method ?? 'n/a');
                                $badgeConfig = [
                                    'cash' => ['icon' => '💵', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                    'debit_card' => ['icon' => '💳', 'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                                    'credit_card' => ['icon' => '💳', 'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                                    'n/a' => ['icon' => '❓', 'bg' => 'bg-gray-200', 'text' => 'text-gray-600'],
                                ];
                                $config = $badgeConfig[$method] ?? $badgeConfig['n/a'];
                            @endphp

                <div
                                class="bg-white rounded-2xl shadow p-4 border border-gray-200 flex flex-col justify-between min-h-[300px]">
                                <div class="flex-grow">
                                    <h2 class="text-lg font-semibold mb-2">Transaction #{{ $transaction->id }}</h2>

                                    <ul class="space-y-1">
                                        @foreach ($transaction->items as $item)
                                            <li class="flex justify-between border-b py-1 text-sm">
                                                <div>
                                                    <div class="font-medium">{{ $item->item->name }}</div>
                                                    <div class="text-gray-500 text-xs">Qty: {{ $item->quantity }} ×
                                                        ₱{{ number_format($item->price, 2) }}</div>
                                                </div>
                                                <div class="font-semibold text-right">
                                                    ₱{{ number_format($item->price * $item->quantity, 2) }}
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Total at the bottom, just above payment method --}}
                                <div class="mt-3 flex justify-between items-center border-t pt-2 text-sm font-semibold">
                                    <span>Total:</span>
                                    <span>
                                        ₱{{ number_format($transaction->items->sum(fn($i) => $i->price * $i->quantity), 2) }}
                                    </span>
                                </div>

                                {{-- Bottom-left payment method badge --}}
                                <div class="mt-4 text-sm self-start">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full font-medium shadow-sm {{ $config['bg'] }} {{ $config['text'] }} hover:brightness-105 transition">
                                        {{ $config['icon'] }}
                                        {{ ucfirst($transaction->payment_method ?? 'N/A') }}
                                    </span>
                                </div>
                            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    </div>
</x-layout>