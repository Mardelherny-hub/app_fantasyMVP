<div>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                <select wire:model.live="type" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">{{ __('All') }}</option>
                    <option value="1">{{ __('Purchase') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('From') }}</label>
                <input type="date" wire:model.live="dateFrom" class="w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('To') }}</label>
                <input type="date" wire:model.live="dateTo" class="w-full rounded-lg border-gray-300 shadow-sm">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">League</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transfers as $transfer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">#{{ $transfer->id }}</td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $transfer->player->full_name }}</td>
                            <td class="px-6 py-4 text-sm">
                                {{ $transfer->fromTeam ? $transfer->fromTeam->name : __('Free Agent') }}
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $transfer->toTeam->name }}</td>
                            <td class="px-6 py-4 text-sm">{{ $transfer->league->name }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($transfer->price, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $transfer->type_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $transfer->effective_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">{{ __('No transfers found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $transfers->links() }}</div>
    </div>
    <div wire:loading class="fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow">{{ __('Loading...') }}</div>
</div>