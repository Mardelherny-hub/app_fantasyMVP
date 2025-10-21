@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Market Dashboard') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Overview of market activity') }}</p>
        </div>
        
        <!-- League Filter -->
        <div class="w-64">
            <select 
                id="league-filter" 
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                onchange="window.location.href = '{{ route('admin.market.index') }}?league_id=' + this.value"
            >
                <option value="">{{ __('All Leagues') }}</option>
                @foreach($leagues as $league)
                    <option value="{{ $league->id }}" {{ $leagueId == $league->id ? 'selected' : '' }}>
                        {{ $league->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Transactions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Total Transactions') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_transactions']) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Total Value Moved') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($stats['total_value_moved'], 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Listings -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Active Listings') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['active_listings']) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Offers -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Pending Offers') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['pending_offers']) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Transfers Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Recent Activity') }}</h3>
            <canvas id="transfersChart" height="200"></canvas>
        </div>

        <!-- Additional Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Market Statistics') }}</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">{{ __('Revenue (Commissions)') }}</span>
                    <span class="font-semibold text-gray-900">${{ number_format($stats['revenue'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">{{ __('Average Price') }}</span>
                    <span class="font-semibold text-gray-900">${{ number_format($stats['avg_price'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600">{{ __('Acceptance Rate') }}</span>
                    <span class="font-semibold text-gray-900">{{ number_format($stats['acceptance_rate'], 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Players and Teams -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Players -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Most Sold Players') }}</h3>
            <div class="space-y-3">
                @forelse($topPlayers as $index => $player)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-gray-400 w-8">{{ $index + 1 }}</span>
                            <span class="ml-3 text-gray-900">{{ $player['player'] }}</span>
                        </div>
                        <span class="text-sm font-medium text-blue-600">{{ $player['sales'] }} {{ __('sales') }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">{{ __('No data available') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Top Teams -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Most Active Teams') }}</h3>
            <div class="space-y-3">
                @forelse($topTeams as $index => $team)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-gray-400 w-8">{{ $index + 1 }}</span>
                            <span class="ml-3 text-gray-900">{{ $team['team'] }}</span>
                        </div>
                        <span class="text-sm font-medium text-green-600">{{ $team['purchases'] }} {{ __('purchases') }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">{{ __('No data available') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('transfersChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_column(array_reverse($chartData), 'date')),
            datasets: [{
                label: '{{ __("Transfers") }}',
                data: @json(array_column(array_reverse($chartData), 'count')),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection