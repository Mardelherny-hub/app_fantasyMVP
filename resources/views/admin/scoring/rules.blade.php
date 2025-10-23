// resources/views/admin/scoring/rules.blade.php
<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Scoring Rules') }}</h1>
    </x-slot>

    <div class="py-6 space-y-6">
        @foreach($seasons as $season)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $season->name }}</h2>
                </div>

                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Action') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Code') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Points') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($season->scoringRules as $rule)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $rule->label }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $rule->code }}</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-3 py-1 rounded-full font-semibold
                                            {{ $rule->points > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $rule->points > 0 ? '+' : '' }}{{ $rule->points }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">
                                        {{ __('No scoring rules defined') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>