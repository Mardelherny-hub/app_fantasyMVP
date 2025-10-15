<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Dashboard') }}</h1>
            <p class="text-sm text-gray-500">{{ __('Welcome back') }}, {{ Auth::user()->name }}</p>
        </div>
    </x-slot>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        {{-- Total Users --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Users') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">
                <span class="font-medium text-green-600">+{{ $recentActivity['new_users_week'] }}</span> {{ __('this week') }}
            </div>
        </div>

        {{-- Active Leagues --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">{{ __('Active Leagues') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_leagues']) }}</p>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">
                {{ __('Leagues currently running') }}
            </div>
        </div>

        {{-- Total Questions --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Questions') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_questions']) }}</p>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">
                {{ __('Active quiz questions') }}
            </div>
        </div>

        {{-- Active Users Today --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">{{ __('Active Today') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['users_today']) }}</p>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">
                {{ __('Users logged in today') }}
            </div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Recent Activity --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Activity This Week') }}</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ __('New Users') }}</p>
                            <p class="text-xs text-gray-500">Last 7 days</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ $recentActivity['new_users_week'] }}</span>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ __('Quiz Attempts') }}</p>
                            <p class="text-xs text-gray-500">Last 7 days</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ $recentActivity['quiz_attempts_week'] }}</span>
                </div>

                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ __('Completed Quizzes') }}</p>
                            <p class="text-xs text-gray-500">Last 7 days</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ $recentActivity['completed_quizzes_week'] }}</span>
                </div>
            </div>
        </div>

        {{-- Top Users --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Top Quiz Players') }}</h3>
            
            <div class="space-y-3">
                @forelse($topUsers as $index => $user)
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full 
                                            {{ 
                                                $index === 0 ? 'bg-yellow-100 text-yellow-600' : 
                                                ($index === 1 ? 'bg-gray-100 text-gray-600' : 
                                                ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-500')) 
                                            }}
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">
                            {{ $user->quiz_attempts_count }} {{ __('quizzes') }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('No quiz activity yet') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Leagues --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent Leagues') }}</h3>
            <a href="{{-- r·oute('admin.fantasy.leagues.index', ['locale' => app()->getLocale()]) --}}·" 
               class="text-sm font-medium text-blue-600 hover:text-blue-700">
                {{ __('View all') }} →
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                        {{-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Owner')}} </th> --}}
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Participants') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200"> 
                    @forelse($recentLeagues as $league)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $league->name }}</div>
                                <div class="text-xs text-gray-500">{{ $league->code }}</div>
                            </td>
                            {{-- <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $league->owner->name }}
                            </td> --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $league->fantasy_teams_count ?? 0 }} / {{ $league->max_participants }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $league->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $league->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $league->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                {{ __('No leagues created yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>