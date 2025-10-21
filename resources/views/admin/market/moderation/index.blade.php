@extends('layouts.admin')

@section('title', __('Moderación del Mercado'))

@section('content')
<div class="container mx-auto px-4 py-6">
    @livewire('admin.market.moderation-panel', ['selectedLeague' => $selectedLeague])
</div>
@endsection