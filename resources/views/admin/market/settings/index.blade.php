@extends('layouts.admin')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Market Settings') }}</h1>
    @livewire('admin.market.market-settings-form')
</div>
@endsection