<div>
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search Player') }}</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Player name...') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <!-- League Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('League') }}</label>
                <select wire:model.live="leagueId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('All Leagues') }}</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select wire:model.live="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('All') }}</option>
                    <option value="0">{{ __('Active') }}</option>
                    <option value="1">{{ __('Sold') }}</option>
                    <option value="2">{{ __('Withdrawn') }}</option>
                    <option value="3">{{ __('Expired') }}</option>
                </select>
            </div>

            <!-- Min Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Min Price') }}</label>
                <input 
                    type="number" 
                    wire:model.live.debounce.500ms="minPrice"
                    placeholder="0.00"
                    step="0.01"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <!-- Max Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Max Price') }}</label>
                <input 
                    type="number" 
                    wire:model.live.debounce.500ms="maxPrice"
                    placeholder="999.99"
                    step="0.01"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Player') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Seller') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('League') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Offers') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($listings as $listing)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $listing->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $listing->player->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $listing->player->position }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $listing->fantasyTeam->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $listing->league->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${{ number_format($listing->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $listing->offers->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        0 => 'bg-green-100 text-green-800',
                                        1 => 'bg-blue-100 text-blue-800',
                                        2 => 'bg-gray-100 text-gray-800',
                                        3 => 'bg-red-100 text-red-800',
                                    ];
                                    $statusLabels = [
                                        0 => __('Active'),
                                        1 => __('Sold'),
                                        2 => __('Withdrawn'),
                                        3 => __('Expired'),
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$listing->status] ?? '' }}">
                                    {{ $statusLabels[$listing->status] ?? __('Unknown') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $listing->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($listing->status === 0)
                                    <form action="{{ route('admin.market.listings.cancel', $listing) }}" method="POST" 
                                          onsubmit="return confirm('{{ __('Are you sure you want to cancel this listing?') }}')">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            {{ __('Cancel') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No listings found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $listings->links() }}
        </div>
    </div>

    <!-- Loading -->
    <div wire:loading class="fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg">
        {{ __('Loading...') }}
    </div>
</div>