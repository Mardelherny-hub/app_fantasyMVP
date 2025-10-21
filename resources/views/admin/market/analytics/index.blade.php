@extends('layouts.admin')
@section('title', __('Analytics del Mercado'))
@section('content')
<div class="container mx-auto px-4 py-6">
    @livewire('admin.market.analytics-panel', [
        'selectedLeague' => $selectedLeague,
        'currentSeason' => $currentSeason
    ])
</div>
@endsection