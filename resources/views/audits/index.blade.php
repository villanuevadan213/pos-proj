<x-layout>
    <x-slot name="header">
        Audit Page
    </x-slot>

    <div class="space-y-4">
        @if(session('success') || session('error'))
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="bg-red-500 text-white p-4 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
        @endif

        <div class="bg-white p-6 overflow-hidden shadow-xl sm:rounded-lg">
            {{-- <x-button href="/audits/create">Add Audit</x-button> --}}
            <x-modal></x-modal>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 bg-white shadow-md rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Title
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Product Control #
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Basket #
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Serial #
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Tracking #
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($audits as $audit)
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $audit->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $audit->item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $audit->product_control_no }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $audit->basket_no }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $audit->serial_no }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $audit->tracking->tracking_no }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $audit->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $audits->links() }}
            </div>
        </div>
    </div>
</x-layout>