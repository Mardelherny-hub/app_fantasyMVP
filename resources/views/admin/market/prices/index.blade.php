@extends('layouts.admin')

@section('title', __('Gestión de Precios'))

@section('content')
<div class="container mx-auto px-4 py-6">
    @livewire('admin.market.prices-manager', ['season' => $currentSeason])
</div>
@endsection