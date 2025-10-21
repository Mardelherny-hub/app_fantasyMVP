<div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select League') }}</label>
            <select wire:model.live="selectedLeagueId" class="w-full md:w-1/2 rounded-lg border-gray-300 shadow-sm">
                @foreach($leagues as $league)
                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                @endforeach
            </select>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Max Price Multiplier') }}</label>
                    <input type="number" step="0.01" wire:model="max_multiplier" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <p class="mt-1 text-sm text-gray-500">{{ __('Maximum price = Market Value × Multiplier') }}</p>
                    @error('max_multiplier') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Offer Cooldown (hours)') }}</label>
                    <input type="number" wire:model="min_offer_cooldown_h" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <p class="mt-1 text-sm text-gray-500">{{ __('Minimum time between offers') }}</p>
                    @error('min_offer_cooldown_h') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="trade_window_open" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2 text-sm font-medium text-gray-700">{{ __('Market Open') }}</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Allow transfers and offers') }}</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="loan_allowed" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2 text-sm font-medium text-gray-700">{{ __('Loans Allowed') }}</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Enable loan system (Not in MVP)') }}</p>
                </div>
            </div>

            <div class="pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ __('Save Settings') }}
                </button>
            </div>
        </form>
    </div>

    @if (session()->has('success'))
        <div class="mt-4 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div wire:loading class="fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow">
        {{ __('Saving...') }}
    </div>
</div>