<div>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search Player') }}</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Player name...') }}" class="w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('League') }}</label>
                <select wire:model.live="leagueId" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">{{ __('All Leagues') }}</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select wire:model.live="status" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">{{ __('All') }}</option>
                    <option value="0">{{ __('Pending') }}</option>
                    <option value="1">{{ __('Accepted') }}</option>
                    <option value="2">{{ __('Rejected') }}</option>
                    <option value="3">{{ __('Expired') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Player</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buyer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">League</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asked</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Offered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">#{{ $offer->id }}</td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $offer->listing->player->full_name }}</td>
                            <td class="px-6 py-4 text-sm">{{ $offer->listing->fantasyTeam->name }}</td>
                            <td class="px-6 py-4 text-sm">{{ $offer->buyerTeam->name }}</td>
                            <td class="px-6 py-4 text-sm">{{ $offer->listing->league->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">${{ number_format($offer->listing->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-medium">
                                ${{ number_format($offer->offered_price, 2) }}
                                @if($offer->offered_price > $offer->listing->price)
                                    <span class="text-green-600">↑</span>
                                @elseif($offer->offered_price < $offer->listing->price)
                                    <span class="text-red-600">↓</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $c = [0=>'bg-yellow-100 text-yellow-800',1=>'bg-green-100 text-green-800',2=>'bg-red-100 text-red-800',3=>'bg-gray-100 text-gray-800'];
                                    $l = [0=>__('Pending'),1=>__('Accepted'),2=>__('Rejected'),3=>__('Expired')];
                                @endphp
                                <span class="px-2 text-xs font-semibold rounded-full {{ $c[$offer->status] }}">{{ $l[$offer->status] }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $offer->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">{{ __('No offers found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $offers->links() }}</div>
    </div>
    <div wire:loading class="fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow">{{ __('Loading...') }}</div>
</div>